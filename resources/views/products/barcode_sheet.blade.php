<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Barcode Labels – Printable</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        @page { size: A4; margin: 15mm 10mm; }
        html, body { height: 100%; margin: 0; padding: 0; background: white; }
        .page {
            height: 100%;
            page-break-after: always;
            break-after: page;
            display: flex;
            flex-direction: column;
        }
        .page:last-child { page-break-after: auto; }
        .labels-grid {
            display: grid;
            grid-template-columns: repeat({{ $gridCols }}, 1fr);
            grid-template-rows: repeat({{ $gridRows }}, 1fr);
            gap: 8px 12px;
            flex: 1;
        }
        .label {
            border: 1px dashed #aaa;
            padding: 8px 6px;
            text-align: center;
            background: white;
            break-inside: avoid;
            page-break-inside: avoid;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .barcode img {
            max-width: 95%;
            height: auto;
        }
        .print-control {
            text-align: center;
            margin-bottom: 20px;
        }
        .print-control button {
            padding: 10px 20px;
            font-size: 16px;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .page-info {
            text-align: center;
            font-size: 9pt;
            margin-top: 5px;
            color: #555;
        }
        @media print {
            .print-control { display: none; }
            .label { border: 1px dotted #ccc; }
        }
    </style>
</head>
<body>
<div class="print-control">
    <button onclick="window.print()">🖨️ Print / Save as PDF</button>
    <p style="font-size:12px; margin-top:8px;">
        {{ count($pages) }} page(s) – {{ $gridCols }} columns × {{ $gridRows }} rows.<br>
        Use browser print → Save as PDF, then print on A4 sticker paper.
    </p>
</div>

@if(count($pages) == 0)
    <p>No labels selected.</p>
@else
    @foreach($pages as $pageIndex => $labels)
        <div class="page">
            <div class="labels-grid">
                @for($row = 0; $row < $gridRows; $row++)
                    @for($col = 0; $col < $gridCols; $col++)
                        @php $index = $row * $gridCols + $col; @endphp
                        @if(isset($labels[$index]))
                            <div class="label">
                                <div class="barcode">
                                    <img src="data:image/png;base64,{{ $labels[$index]['barcode_image'] }}" alt="barcode">
                                </div>
                            </div>
                        @else
                            <div class="label" style="visibility: hidden;"></div>
                        @endif
                    @endfor
                @endfor
            </div>
            <div class="page-info">
                Page {{ $pageIndex + 1 }} — {{ count($labels) }} labels ({{ $gridCols }}×{{ $gridRows }} grid)
            </div>
        </div>
    @endforeach
@endif
</body>
</html>
