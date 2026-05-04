<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function edit()
    {
        return view('profile.edit', ['user' => auth()->user()]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'position' => ['nullable', 'string', 'max:50'],
        ]);

        auth()->user()->update($request->only('name', 'position'));

        return back()->with('success', 'Perfil atualizado com sucesso!');
    }
}
