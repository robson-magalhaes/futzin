<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\Poll;
use App\Models\PollVote;
use Illuminate\Http\Request;

class PollController extends Controller
{
    /** Criar enquete em um grupo (admin only) */
    public function store(Request $request, Group $group)
    {
        $member = $group->members()->where('user_id', auth()->id())->first();
        abort_unless($member && $member->pivot->role === 'admin', 403, 'Apenas admins podem criar enquetes.');

        $validated = $request->validate([
            'title'     => 'required|string|max:255',
            'type'      => 'required|in:mvp,rating',
            'match_id'  => 'nullable|exists:matches,id',
            'closes_at' => 'nullable|date|after:now',
        ]);

        $poll = $group->polls()->create([
            ...$validated,
            'created_by' => auth()->id(),
            'status'     => 'open',
        ]);

        return redirect()->route('polls.show', $poll)
            ->with('success', 'Enquete criada com sucesso!');
    }

    /** Exibir enquete: formulário de voto ou resultados */
    public function show(Poll $poll)
    {
        $member = $poll->group->members()->where('user_id', auth()->id())->first();
        abort_unless($member, 403, 'Acesso negado.');

        $userRole  = $member->pivot->role;
        $userVote  = $poll->votes()->where('voter_id', auth()->id())->first();
        $members   = $poll->group->members()->get();

        // Para rating: para cada membro, nota que o usuário deu (se já votou)
        $myRatings = [];
        if ($poll->type === 'rating' && $userVote) {
            $myRatings = $poll->votes()
                ->where('voter_id', auth()->id())
                ->pluck('rating', 'candidate_id')
                ->toArray();
            // No modelo de rating, voter_id vota em CADA membro com uma nota
            // então pode ter N votes por voter. "unique(poll_id, voter_id)" não se aplica
            // diretamente — veja nota no store rating abaixo.
        }

        // Resultados MVP: contagem de votos por candidato
        $mvpResults = [];
        if ($poll->type === 'mvp') {
            $mvpResults = $poll->votes()
                ->with('candidate')
                ->selectRaw('candidate_id, count(*) as total')
                ->groupBy('candidate_id')
                ->orderByDesc('total')
                ->get();
        }

        // Resultados rating: média de notas por membro
        $ratingResults = [];
        if ($poll->type === 'rating') {
            $ratingResults = $poll->votes()
                ->with('candidate')
                ->selectRaw('candidate_id, round(avg(rating), 2) as avg_rating, count(*) as votes_count')
                ->groupBy('candidate_id')
                ->orderByDesc('avg_rating')
                ->get();
        }

        return view('polls.show', compact(
            'poll', 'userRole', 'userVote', 'members',
            'myRatings', 'mvpResults', 'ratingResults'
        ));
    }

    /** Registrar voto */
    public function vote(Request $request, Poll $poll)
    {
        $member = $poll->group->members()->where('user_id', auth()->id())->first();
        abort_unless($member, 403, 'Acesso negado.');
        abort_unless($poll->isOpen(), 422, 'Esta enquete está encerrada.');

        if ($poll->type === 'mvp') {
            // Um voto por enquete
            $request->validate([
                'candidate_id' => 'required|exists:users,id',
            ]);

            // Impede votar em si mesmo e confirma que candidato é membro
            $candidateMember = $poll->group->members()
                ->where('user_id', $request->candidate_id)->first();
            abort_unless($candidateMember, 422, 'Candidato não é membro do grupo.');

            $existingVote = $poll->votes()->where('voter_id', auth()->id())->first();
            abort_if($existingVote, 422, 'Você já votou nesta enquete.');

            PollVote::create([
                'poll_id'      => $poll->id,
                'voter_id'     => auth()->id(),
                'candidate_id' => $request->candidate_id,
            ]);

        } elseif ($poll->type === 'rating') {
            // Uma nota por jogador (múltiplas linhas, por isso unique viola se tentarmos inserir de novo)
            // Validação: ratings é um array keyed por user_id
            $request->validate([
                'ratings'   => 'required|array|min:1',
                'ratings.*' => 'required|integer|min:1|max:10',
            ]);

            // Verifica se já votou (qualquer vote com voter_id)
            $existingVotes = $poll->votes()->where('voter_id', auth()->id())->count();
            abort_if($existingVotes > 0, 422, 'Você já enviou suas notas nesta enquete.');

            foreach ($request->ratings as $userId => $rating) {
                // Confirma membro
                $candidateMember = $poll->group->members()->where('user_id', $userId)->first();
                if (!$candidateMember) continue;

                PollVote::create([
                    'poll_id'      => $poll->id,
                    'voter_id'     => auth()->id(),
                    'candidate_id' => $userId,
                    'rating'       => (int) $rating,
                ]);
            }
        }

        return redirect()->route('polls.show', $poll)
            ->with('success', 'Voto registrado com sucesso!');
    }

    /** Encerrar enquete (admin only) */
    public function close(Poll $poll)
    {
        $member = $poll->group->members()->where('user_id', auth()->id())->first();
        abort_unless($member && $member->pivot->role === 'admin', 403, 'Apenas admins podem encerrar enquetes.');

        $poll->update(['status' => 'closed']);

        return redirect()->route('polls.show', $poll)
            ->with('success', 'Enquete encerrada.');
    }
}
