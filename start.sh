#!/bin/bash

# Iniciar Futzin - Execute em 2 terminais diferentes

if [ "$1" == "server" ]; then
    echo "Iniciando servidor Laravel..."
    php artisan serve
elif [ "$1" == "dev" ]; then
    echo "Iniciando dev server frontend..."
    npm run dev
else
    echo ""
    echo "=========================================="
    echo "        FUTZIN - Inicializacao Rapida"
    echo "=========================================="
    echo ""
    echo "Execute em 2 terminais diferentes:"
    echo ""
    echo "   Terminal 1:"
    echo "   ./start.sh server"
    echo ""
    echo "   Terminal 2:"
    echo "   ./start.sh dev"
    echo ""
    echo "Depois acesse:"
    echo "   http://localhost:5173"
    echo "   Email: admin@futzin.com"
    echo "   Senha: password"
    echo ""
fi
