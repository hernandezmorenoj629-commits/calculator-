<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cotización - {{ $empresa }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --brand-color: {{ $color }};
        }

        /* Ajuste Estricto Tamaño Carta */
        @page {
            size: letter;
            margin: 0;
        }

        body {
            background-color: #f1f5f9;
            font-family: 'Inter', sans-serif;
            color: #334155;
            margin: 0;
            padding: 0;
            -webkit-print-color-adjust: exact;
        }

        .factura-container {
            background: white;
            margin: 10mm auto;
            width: 216mm;
            min-height: 279mm;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
            display: flex;
            flex-direction: column;
        }

        .accent-bar {
            height: 8px;
            background-color: var(--brand-color);
            width: 100%;
        }

        .text-brand { color: var(--brand-color) !important; }
        .bg-brand { background-color: var(--brand-color) !important; color: white !important; }

        .logo-factura {
            max-height: 65px;
            width: auto;
            object-fit: contain;
        }

        /* Tablas */
        .table {
            table-layout: fixed; /* Mantiene proporciones fijas en las columnas */
            width: 100%;
        }

        .table thead th {
            background-color: var(--brand-color) !important;
            color: white !important;
            text-transform: uppercase;
            font-size: 0.75rem;
            text-align: center;
            border: none;
            padding: 12px;
        }

        .table tbody td {
            text-align: center;
            vertical-align: middle;
            border-bottom: 1px solid #f1f5f9;
            font-size: 0.9rem;
            padding: 12px;
        }

        .table td:first-child, .table th:first-child {
            text-align: left;
            padding-left: 25px;
        }

        /* Ajuste para evitar desbordamiento de texto largo en descripciones */
        .col-descripcion {
            word-wrap: break-word;
            word-break: break-all;
            white-space: pre-line;
            text-align: left !important;
        }

        .info-label {
            font-size: 0.7rem;
            text-transform: uppercase;
            font-weight: 800;
            color: var(--brand-color);
            margin-bottom: 2px;
            display: block;
        }

        .value-text {
            font-size: 0.9rem;
            font-weight: 600;
            color: #1e293b;
        }

        .totals-section {
            background-color: #f8fafc;
            border-radius: 8px;
            padding: 15px;
            border: 1px solid #e2e8f0;
        }

        /* Firma de Responsable */
        .signature-section {
            margin-top: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .signature-line {
            width: 200px;
            border-top: 2px solid var(--brand-color);
            margin-bottom: 5px;
        }

        @media print {
            .no-print { display: none !important; }
            body { background: white; }
            .factura-container {
                margin: 0;
                box-shadow: none;
                width: 216mm;
                height: 279mm;
            }
        }
    </style>
</head>
<body>

<div class="factura-container">
    <div class="accent-bar"></div>

    <div class="p-5 pb-4">
        <div class="row align-items-center">
            <div class="col-6">
                <img src="/imagen/{{ $logo }}" alt="Logo" class="logo-factura mb-3">
                <h3 class="fw-800 mb-0 text-brand">{{ $empresa }}</h3>
                <div class="text-muted small mt-1">
                    <p class="mb-0">Emisión: {{ $fecha }} | {{ $hora }}</p>
                    @if(isset($nuevoContador))
                    <p class="mb-0 fw-bold">No. Cotización: {{ $nuevoContador }}</p>
                    @endif
                </div>
            </div>
            <div class="col-6 text-end">
                <span class="badge border border-primary text-primary px-3 py-2 mb-3 text-uppercase fw-bold">Cotización Oficial</span>

                <div class="row text-start justify-content-end">
                    <div class="col-10 border-start ps-3 mb-2">
                        <span class="info-label">Cliente</span>
                        <h5 class="fw-bold text-dark mb-1">{{ $cliente }}</h5>
                        @if(isset($ruc) && $ruc !== 'N/A')
                        <span class="text-muted small d-block"><strong>RUC/Cédula:</strong> {{ $ruc }}</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="mx-5 mb-4 p-3 bg-light rounded-3 border">
        <div class="row g-3">
            <div class="col-6 border-end pe-3">
                <div class="mb-2">
                    <span class="info-label"><i class="fas fa-address-book me-1"></i> Contacto de Entrega</span>
                    <span class="value-text">{{ $contacto ?? 'N/A' }}</span>
                </div>
                <div class="mb-2">
                    <span class="info-label"><i class="fas fa-phone me-1"></i> Teléfono</span>
                    <span class="value-text">{{ $telefono ?? 'N/A' }}</span>
                </div>
                <div>
                    <span class="info-label"><i class="fas fa-map-marker-alt me-1"></i> Dirección exacta</span>
                    <span class="value-text text-muted" style="font-size: 0.85rem;">{{ $direccion ?? 'N/A' }}</span>
                </div>
            </div>

            <div class="col-6 ps-3 d-flex flex-column justify-content-center">
                <div class="mb-2">
                    <span class="info-label">Zona de Envío</span>
                    <span class="fw-bold text-dark small">{{ $zona }}</span>
                </div>
                <div class="mb-2">
                    <span class="info-label">Ruta / Carretera</span>
                    <span class="fw-bold text-dark small">{{ $ruta ?? 'Ruta' }}</span>
                </div>
                <div>
                    <span class="info-label">Sub-Ruta (Destino)</span>
                    <span class="fw-bold text-dark small">{{ $subruta ?? 'Sub-Ruta' }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="px-5 flex-grow-1">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th style="width: 50%">Descripción</th>
                        <th style="width: 10%">Cant.</th>
                        <th style="width: 20%">Precio Unit.</th>
                        <th style="width: 20%">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($servicios as $s)
                    <tr>
                        <td class="col-descripcion"><span class="fw-600 text-dark">{{ $s['desc'] ?? $s['nombre'] }}</span></td>
                        <td class="fw-bold">{{ $s['cant'] ?? $s['cantidad'] }}</td>
                        <td class="text-muted">{{ $moneda_simbolo ?? $moneda }} {{ number_format($s['precio'], 2) }}</td>
                        <td class="fw-bold text-brand">{{ $moneda_simbolo ?? $moneda }} {{ number_format(($s['cant'] ?? $s['cantidad']) * $s['precio'], 2) }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center py-5 text-muted">No hay servicios registrados</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="row justify-content-end mt-3">
            <div class="col-5">
                <div class="totals-section">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="text-muted small">Transporte:</span>
                        <span class="fw-semibold small text-dark">{{ $moneda_simbolo ?? $moneda }} {{ number_format($transporte, 2) }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-1">
                        <span class="text-muted small">Descuento:</span>
                        <span class="text-danger fw-semibold small">-{{ $moneda_simbolo ?? $moneda }} {{ number_format($descuento, 2) }}</span>
                    </div>
                    <div class="d-flex justify-content-between border-top pt-2 mb-1">
                        <span class="fw-bold text-dark small">SUBTOTAL:</span>
                        <span class="fw-bold text-dark small">{{ $moneda_simbolo ?? $moneda }} {{ number_format($subtotal, 2) }}</span>
                    </div>
                    @if($iva > 0)
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted small">IVA (15%):</span>
                        <span class="fw-semibold small text-dark">{{ $moneda_simbolo ?? $moneda }} {{ number_format($iva, 2) }}</span>
                    </div>
                    @endif
                    <div class="d-flex justify-content-between bg-brand p-2 rounded text-white mt-2">
                        <span class="fw-bold">TOTAL:</span>
                        <span class="fw-bold h5 mb-0">{{ $moneda_simbolo ?? $moneda }} {{ number_format($total, 2) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="p-5 text-center mt-auto">
        <div class="row justify-content-center mb-4">
            <div class="col-6 signature-section">
                <div class="signature-line"></div>
                <span class="fw-bold text-dark small text-uppercase">{{ $responsable_nombre ?? 'Jammy Silva' }}</span>
                <span class="text-muted d-block" style="font-size: 0.75rem;">{{ $responsable_cargo ?? 'Supervisora - Coordinadora' }}</span>
                @if(isset($responsable_tel))
                <span class="text-dark fw-semibold" style="font-size: 0.75rem;">TEL: {{ $responsable_tel }}</span>
                @endif
            </div>
        </div>

        <div class="border-top pt-3">
            <p class="mb-1 fw-bold text-brand">¡Gracias por su preferencia!</p>
            <p class="text-dark mb-0" style="font-size: 0.85rem; font-weight: 600;">
                Esta proforma tiene una validez de 30 días calendario.
            </p>
            <p class="text-muted mb-0" style="font-size: 0.75rem;">
                Vence el: {{ \Carbon\Carbon::parse($fecha)->addDays(30)->format('d/m/Y') }}
            </p>
        </div>
    </div>
</div>

<div class="text-center my-5 no-print">
    <button onclick="window.print()" class="btn btn-primary btn-lg px-5 shadow rounded-pill">
        <i class="fas fa-print me-2"></i>Imprimir Cotización
    </button>
    <a href="{{ route('cotizador.index') }}" class="btn btn-outline-secondary btn-lg ms-2 rounded-pill">
        <i class="fas fa-undo me-2"></i>Nueva Cotización
    </a>
</div>

</body>
</html>
