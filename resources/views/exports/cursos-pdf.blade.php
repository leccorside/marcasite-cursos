<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Cursos - Relatório</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #333; color: white; font-weight: bold; }
        tr:nth-child(even) { background-color: #f2f2f2; }
        h1 { color: #333; }
        .currency { text-align: right; }
    </style>
</head>
<body>
    <h1>Relatório de Cursos</h1>
    <p>Gerado em: {{ now()->format('d/m/Y H:i:s') }}</p>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nome</th>
                <th>Categoria</th>
                <th>Valor (R$)</th>
                <th>Vagas Total</th>
                <th>Vagas Disponíveis</th>
                <th>Início Inscrições</th>
                <th>Fim Inscrições</th>
                <th>Ativo</th>
                <th>Data de Cadastro</th>
            </tr>
        </thead>
        <tbody>
            @foreach($cursos as $curso)
            <tr>
                <td>{{ $curso->id }}</td>
                <td>{{ $curso->nome }}</td>
                <td>{{ $curso->categoria ?? '-' }}</td>
                <td class="currency">R$ {{ number_format($curso->valor, 2, ',', '.') }}</td>
                <td>{{ $curso->vagas_total }}</td>
                <td>{{ $curso->vagas_disponiveis }}</td>
                <td>{{ $curso->data_inicio_inscricoes ? \Carbon\Carbon::parse($curso->data_inicio_inscricoes)->format('d/m/Y') : '-' }}</td>
                <td>{{ $curso->data_fim_inscricoes ? \Carbon\Carbon::parse($curso->data_fim_inscricoes)->format('d/m/Y') : '-' }}</td>
                <td>{{ $curso->ativo ? 'Sim' : 'Não' }}</td>
                <td>{{ $curso->created_at->format('d/m/Y H:i') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
