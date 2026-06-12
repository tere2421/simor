<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<style>
    body { font-family: Arial, sans-serif; font-size: 10px; color: #1e293b; }
    h2   { font-size: 14px; margin-bottom: 4px; }
    p    { font-size: 9px; color: #64748b; margin-bottom: 12px; }
    table { width: 100%; border-collapse: collapse; font-size: 8.5px; }
    th   { background: #1e3a5f; color: #fff; padding: 5px 6px; text-align: center; white-space: nowrap; }
    th.left { text-align: left; }
    td   { padding: 4px 6px; border: 1px solid #e2e8f0; text-align: center; }
    td.staff { text-align: left; background: #f8fafc; font-weight: bold; }
    .pagi  { background: #dbeafe; color: #1e40af; font-weight: bold; }
    .siang { background: #fef9c3; color: #854d0e; font-weight: bold; }
    .malam { background: #ede9fe; color: #5b21b6; font-weight: bold; }
    .libur { color: #cbd5e1; }
    .footer { margin-top: 16px; font-size: 8px; color: #94a3b8; text-align: right; }
</style>
</head>
<body>
<h2>Jadwal Shift Mingguan — SIMOR Hangry Indonesia</h2>
<p>Periode: {{ $dates->first()->locale('id')->isoFormat('D MMMM YYYY') }} – {{ $dates->last()->locale('id')->isoFormat('D MMMM YYYY') }}</p>

<table>
    <thead>
        <tr>
            <th class="left" style="min-width:140px;">Nama Staff</th>
            <th>Posisi</th>
            @foreach($dates as $d)
                <th>{{ $d->locale('id')->isoFormat('ddd') }}<br>{{ $d->format('d/m') }}</th>
            @endforeach
            <th>Total</th>
        </tr>
    </thead>
    <tbody>
        @foreach($staff as $s)
        @php $cnt = 0; @endphp
        <tr>
            <td class="staff">{{ $s->name }}</td>
            <td>{{ $s->position }}</td>
            @foreach($dates as $d)
                @php $sc = $schedules[$s->id][$d->toDateString()][0] ?? null; if($sc) $cnt++; @endphp
                <td class="{{ $sc ? strtolower(explode(' ', $sc->shift->name)[1] ?? '') : 'libur' }}">
                    {{ $sc ? $sc->shift->name : '—' }}
                </td>
            @endforeach
            <td><b>{{ $cnt }}x</b></td>
        </tr>
        @endforeach
    </tbody>
</table>

<div class="footer">
    Dicetak oleh SIMOR · {{ now()->locale('id')->isoFormat('D MMMM YYYY, HH:mm') }}
</div>
</body>
</html>
