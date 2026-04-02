<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pedido confirmado</title>
</head>
<body style="font-family: ui-sans-serif, system-ui, sans-serif; line-height: 1.5; color: #1f2937; max-width: 640px; margin: 0 auto; padding: 24px; background-color: #f9fafb;">
    <div style="background: #fff; border: 1px solid #e5e7eb; border-radius: 12px; padding: 28px; box-shadow: 0 1px 3px rgba(0,0,0,0.06);">
        <p style="margin: 0 0 4px 0; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; color: #6b7280;">{{ config('app.name') }}</p>
        <h1 style="font-size: 1.35rem; font-weight: 700; color: #b91c1c; margin: 0 0 8px 0;">Confirmación de pedido</h1>
        <p style="margin: 0 0 20px 0; font-size: 0.875rem; color: #4b5563;">Mismo detalle que en tu carrito — conserva este correo como comprobante.</p>

        <p style="margin: 0 0 16px 0;">Hola <strong>{{ $orders->first()->user->name }}</strong>,</p>
        <p style="margin: 0 0 20px 0; font-size: 0.875rem;">Recibimos tu pedido. A continuación el desglose tipo factura:</p>

        <table style="width: 100%; border-collapse: collapse; font-size: 0.8125rem; margin: 16px 0;">
            <thead>
                <tr style="background: #fef2f2; border-bottom: 2px solid #fecaca;">
                    <th style="text-align: left; padding: 10px 8px; color: #991b1b;">Producto</th>
                    <th style="text-align: center; padding: 10px 8px; color: #991b1b; width: 52px;">Cant.</th>
                    <th style="text-align: right; padding: 10px 8px; color: #991b1b; white-space: nowrap;">P. unit.</th>
                    <th style="text-align: right; padding: 10px 8px; color: #991b1b; white-space: nowrap;">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach($orders as $line)
                <tr style="border-bottom: 1px solid #e5e7eb;">
                    <td style="padding: 12px 8px; vertical-align: top;">
                        <strong style="color: #111827;">{{ $line->pizza->name }}</strong>
                        @if($line->pizza->description && $line->pizza->description !== '')
                            <span style="display: block; font-size: 0.75rem; color: #6b7280; margin-top: 4px;">{{ $line->pizza->description }}</span>
                        @endif
                        @if($line->pizza->ingredients && $line->pizza->ingredients->isNotEmpty())
                            <span style="display: block; font-size: 0.7rem; color: #9ca3af; margin-top: 6px;">
                                Ingredientes: {{ $line->pizza->ingredients->pluck('name')->implode(', ') }}
                            </span>
                        @endif
                    </td>
                    <td style="padding: 12px 8px; text-align: center; vertical-align: top;">{{ $line->quantity }}</td>
                    <td style="padding: 12px 8px; text-align: right; vertical-align: top; white-space: nowrap;">Bs {{ number_format((float) $line->pizza->price, 2) }}</td>
                    <td style="padding: 12px 8px; text-align: right; vertical-align: top; white-space: nowrap;"><strong>Bs {{ number_format((float) $line->total, 2) }}</strong></td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <table style="width: 100%; border-collapse: collapse; font-size: 0.875rem; margin-top: 8px;">
            <tr>
                <td style="padding: 8px 0; text-align: right; color: #6b7280;">Ítems distintos:</td>
                <td style="padding: 8px 0; text-align: right; width: 120px;">{{ $orders->count() }}</td>
            </tr>
            <tr>
                <td style="padding: 8px 0; text-align: right; font-size: 1rem;"><strong>Total a pagar</strong></td>
                <td style="padding: 8px 0; text-align: right; font-size: 1.1rem; color: #b91c1c;"><strong>Bs {{ number_format((float) $orders->sum('total'), 2) }}</strong></td>
            </tr>
        </table>

        <hr style="border: none; border-top: 1px solid #e5e7eb; margin: 20px 0;">

        <table style="width: 100%; font-size: 0.8125rem; color: #4b5563;">
            <tr>
                <td style="padding: 4px 0;"><strong>Referencia pedido</strong></td>
                <td style="padding: 4px 0; text-align: right;">#{{ $orders->first()->id }} @if($orders->count() > 1)({{ $orders->count() }} líneas)@endif</td>
            </tr>
            <tr>
                <td style="padding: 4px 0;"><strong>Cliente</strong></td>
                <td style="padding: 4px 0; text-align: right;">{{ $orders->first()->user->email }}</td>
            </tr>
            <tr>
                <td style="padding: 4px 0;"><strong>Fecha</strong></td>
                <td style="padding: 4px 0; text-align: right;">{{ $orders->first()->ordered_at?->format('d/m/Y H:i') ?? $orders->first()->created_at->format('d/m/Y H:i') }}</td>
            </tr>
        </table>

        <p style="margin-top: 24px; font-size: 0.8125rem; color: #6b7280;">Si tienes alguna consulta sobre este pedido, responde a este correo.</p>
        <p style="font-size: 0.75rem; color: #9ca3af; margin-bottom: 0;">{{ config('app.name') }}</p>
    </div>
</body>
</html>
