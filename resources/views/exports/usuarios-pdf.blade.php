<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Usuários - Relatório</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #333; color: white; font-weight: bold; }
        tr:nth-child(even) { background-color: #f2f2f2; }
        h1 { color: #333; }
    </style>
</head>
<body>
    <h1>Relatório de Usuários</h1>
    <p>Gerado em: {{ now()->format('d/m/Y H:i:s') }}</p>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nome</th>
                <th>Email</th>
                <th>Tipo</th>
                <th>Ativo</th>
                <th>Data de Cadastro</th>
            </tr>
        </thead>
        <tbody>
            @foreach($usuarios as $usuario)
            <tr>
                <td>{{ $usuario->id }}</td>
                <td>{{ $usuario->name }}</td>
                <td>{{ $usuario->email }}</td>
                <td>{{ $usuario->tipo === 'admin' ? 'Administrador' : 'Aluno' }}</td>
                <td>{{ $usuario->ativo ? 'Sim' : 'Não' }}</td>
                <td>{{ $usuario->created_at->format('d/m/Y H:i') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
