<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pedido confirmado</title>
</head>
<body style="font-family: ui-sans-serif, system-ui, sans-serif; line-height: 1.5; color: #1f2937; max-width: 560px; margin: 0 auto; padding: 24px;">
    <h1 style="font-size: 1.25rem; font-weight: 700; color: #b91c1c;">¡Gracias por tu pedido!</h1>
    <p>Hola {{ $order->user->name }},</p>
    <p>Recibimos tu pedido correctamente. Aquí tienes el detalle:</p>

    <table style="width: 100%; border-collapse: collapse; margin: 20px 0; font-size: 0.875rem;">
        <tr>
            <td style="padding: 8px 0; border-bottom: 1px solid #e5e7eb;"><strong>Número de pedido</strong></td>
            <td style="padding: 8px 0; border-bottom: 1px solid #e5e7eb; text-align: right;">#{{ $order->id }}</td>
        </tr>
        <tr>
            <td style="padding: 8px 0; border-bottom: 1px solid #e5e7eb;"><strong>Pizza</strong></td>
            <td style="padding: 8px 0; border-bottom: 1px solid #e5e7eb; text-align: right;">{{ $order->pizza->name }}</td>
        </tr>
        <tr>
            <td style="padding: 8px 0; border-bottom: 1px solid #e5e7eb;"><strong>Cantidad</strong></td>
            <td style="padding: 8px 0; border-bottom: 1px solid #e5e7eb; text-align: right;">{{ $order->quantity }}</td>
        </tr>
        <tr>
            <td style="padding: 8px 0; border-bottom: 1px solid #e5e7eb;"><strong>Total</strong></td>
            <td style="padding: 8px 0; border-bottom: 1px solid #e5e7eb; text-align: right;">S/ {{ number_format((float) $order->total, 2) }}</td>
        </tr>
        <tr>
            <td style="padding: 8px 0;"><strong>Fecha</strong></td>
            <td style="padding: 8px 0; text-align: right;">{{ $order->ordered_at?->format('d/m/Y H:i') ?? $order->created_at->format('d/m/Y H:i') }}</td>
        </tr>
    </table>

    @if($order->pizza->description && $order->pizza->description !== '')
        <p style="font-size: 0.875rem; color: #4b5563;"><strong>Descripción:</strong> {{ $order->pizza->description }}</p>
    @endif

    @if($order->pizza->ingredients && $order->pizza->ingredients->isNotEmpty())
        <p style="font-size: 0.875rem; margin-top: 16px;"><strong>Ingredientes:</strong></p>
        <ul style="margin: 8px 0 0 0; padding-left: 20px; font-size: 0.875rem; color: #4b5563;">
            @foreach($order->pizza->ingredients as $ingredient)
                <li>{{ $ingredient->name }}</li>
            @endforeach
        </ul>
    @endif

    <p style="margin-top: 24px; font-size: 0.875rem; color: #6b7280;">Si tienes alguna consulta, responde a este correo.</p>
    <p style="font-size: 0.875rem; color: #9ca3af;">{{ config('app.name') }}</p>
</body>
</html>
