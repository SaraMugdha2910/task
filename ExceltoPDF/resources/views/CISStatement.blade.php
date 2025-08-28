<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>CIS Statement</title>
    <style>
        @page { size: A4; margin: 28mm 18mm 20mm 18mm; }
        html, body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11px;
            color: #0f3e46;
            line-height: 1.25;
        }
        .sheet { width: 174mm; /* 210 - (18+18) */ }
        .teal { color: #157c96; }
        .header-title {
            font-size: 20px;
            font-weight: 700;
            line-height: 1.35;
            color: #157c96;
            margin: 0 0 2mm 0;
        }
        .rule {
            height: 6px;
            background: #157c96;
            margin: 3mm 0 5mm 0;
        }
        .panel {
            border: 1px solid #92c8cf;
            padding: 5mm 5mm 4mm 5mm;
            margin-bottom: 6mm;
            page-break-inside: avoid;
        }
        .panel-title {
            font-weight: 700;
            color: #157c96;
            font-size: 14px;
            margin: 0 0 3mm 0;
        }
        .grid { display: table; width: 100%; table-layout: fixed; }
        .col { display: table-cell; vertical-align: top; }
        .col-left { width: 58%; padding-right: 4mm; }
        .col-right { width: 42%; padding-left: 4mm; }
        .field { margin: 2.5mm 0; }
        .label { display: block; color: #157c96; font-weight: 700; margin: 0 0 1mm 0; }
        .hint { color: #6da6ad; font-size: 10px; margin-bottom: 1mm; }
        /* Fixed track to align left column inputs exactly */
        .track-left { display: inline-block; width: 95mm; }
        .line-input { border: 1px solid #92c8cf; height: 8mm; }
        .line-input.multiline { height: 18mm; }
        .boxes {
    display: inline-block;   /* instead of flex */
    white-space: nowrap;     /* keeps boxes in one row */
}
 
.box {
    display: inline-block;
    width: 5mm;               /* smaller than before */
    height: 6mm;              /* reduced height */
    border: 1px solid #92c8cf;
    text-align: center;
    vertical-align: middle;
    line-height: 6mm;         /* matches height for vertical centering */
    font-size: 9px;           /* smaller font so digits fit */
    color: #333;
    margin-right: 0.8mm;      /* tighter spacing */
    box-sizing: border-box;   /* includes border in width/height */
    overflow: hidden;         /* prevents spillover */
    text-overflow: clip;
    white-space: nowrap;
}
 
.box:last-child {
    margin-right: 0;
}
 
.box--currency {
    background: #157c96;
    color: #fff;
    border-color: #157c96;
    font-weight: 700;
    font-size: 9px;   /* make sure $ sign also fits neatly */
}
 
 
        .box--currency { background: #157c96; color: #fff; border-color: #157c96; font-weight: 700; }
        .box-slash { color: #92c8cf; font-weight: 700; margin: 0 1mm; }
        .amount-row { margin: 3mm 0; }
        /* Stacked rows for amount label/value */
        .amount-label { display: block; width: 100%; margin-bottom: 1.5mm; }
        .amount-value { display: block; width: 100%; text-align: left; }
        .amount-value .boxes { padding-left: 2mm; }
        .muted { color: #6da6ad; }
        .footer-note { text-align: center; margin-top: 8mm; color: #6da6ad; font-size: 10px; page-break-inside: avoid; }
        /* Keep everything on one page */
        .no-break { page-break-inside: avoid; page-break-before: auto; page-break-after: auto; }
    </style>
</head>
<body>
<div class="sheet no-break">
    <div class="header-title">
        Construction Industry Scheme<br>
        Payment and deduction statement
    </div>
    <div class="rule"></div>
 
    @php
        $pad = function ($value, $length) {
            $s = (string)($value ?? '');
            $s = preg_replace('/\s+/', '', $s);
            return str_split(str_pad($s, $length, ' ', STR_PAD_RIGHT));
        };
        $moneyToBoxes = function ($value, $digits = 6) {
            $num = number_format((float)($value ?? 0), 2, '.', '');
            $num = str_replace(['£', ','], '', $num);
            $parts = explode('.', $num);
            $left = preg_replace('/\D/', '', $parts[0]);
            $left = str_pad($left, $digits, ' ', STR_PAD_LEFT);
            $right = str_pad($parts[1] ?? '00', 2, '0', STR_PAD_RIGHT);
            return [str_split($left), str_split($right)];
        };
        // helper to render a character; ensures empty boxes still show
        $chr = function ($c) {
            return $c === ' ' || $c === '' ? '&nbsp;' : e($c);
        };
        // Normalize tax month end to numeric DDMMYYYY (always DD=05)
        $taxInput = $tax_month_end ?? '';
        $day = '05';
        $month = '';
        $year = '';
        if (!empty($taxInput)) {
            $ts = strtotime($taxInput);
            if ($ts) {
                $month = date('m', $ts);
                $year = date('Y', $ts);
            } else {
                $digits = preg_replace('/\D+/', '', $taxInput);
                if (strlen($digits) >= 6) {
                    $month = substr($digits, 0, 2);
                    $year = substr($digits, -4);
                    $month = str_pad((string)intval($month), 2, '0', STR_PAD_LEFT);
                }
            }
        }
        $taxBoxes = $day . $month . $year; // may be shorter than 8; pad later
 
        // Build amount rows from provided collection or defaults
        $amountRows = $amount_rows ?? [
            ['label' => 'Gross amount paid (Excl VAT) (A)', 'value' => ($gross_amount ?? 0)],
            ['label' => 'Less cost of materials', 'value' => ($material_cost ?? 0)],
            ['label' => 'Amount liable to deduction', 'value' => ($liable_amount ?? 0)],
            ['label' => 'Amount deducted (B)', 'value' => ($deducted_amount ?? 0)],
            ['label' => 'Amount payable (A - B)', 'value' => ($payable_amount ?? 0), 'strong' => true],
        ];


    $ver = $verification_no ?? '';
    $ver = ltrim($ver, 'V');
    [$verification_no_left, $verification_no_right] = array_pad(explode('/', $ver, 2), 2, '');



    $empRef = $employer_tax_ref ?? '';
    [$emp_ref_left, $emp_ref_right] = array_pad(explode('/', $empRef, 2), 2, '');



    @endphp
 
    <div class="panel">
        <div class="panel-title">Contractor details</div>
        <div class="grid">
            <div class="col col-left">
                <div class="field">
                    <span class="label">Contractor’s name</span>
                    <div class="track-left"><div class="line-input">
                   
    {{ $contractor_name }}
 
 
                    </div></div>
                </div>
                <div class="field">
                    <span class="label">Contractor’s address</span>
                    <div class="track-left"><div class="line-input multiline">
                       
<div class="address-input">
    {{ $contractor_address }}
</div>
                    </div></div>
                </div>
            </div>
            <div class="col col-right">
                <div class="field">
                    <span class="label">Payment and deduction made in tax month ended</span>
                    <div class="hint">05 MM YYYY</div>
                    <div class="track-left">
                        <div class="boxes">
                            @foreach($pad($taxBoxes, 8) as $c)
                                <span class="box">{!! $chr($c) !!}</span>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="field" style="margin-top:5mm;">
                    <span class="label">Employer’s Tax Reference</span>
                    <div class="track-left">
                        <div class="boxes">
                            @foreach($pad(($emp_ref_left ?? ''), 3) as $c)
                                <span class="box">{!! $chr($c) !!}</span>
                            @endforeach
                            <span class="box-slash">/</span>
                            @foreach($pad(($emp_ref_right ?? ''), 8) as $c)
                                <span class="box">{!! $chr($c) !!}</span>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
 
    <div class="panel">
        <div class="panel-title">Subcontractor details</div>
        <div class="grid">
            <div class="col col-left">
                <div class="field">
                    <span class="label">Subcontractor’s full name</span>
                    <div class="track-left"><div class="line-input">{{ $subcontractor_name }}</div></div>
                </div>
                <div class="field">
                    <span class="label">Unique Taxpayer reference (UTR)</span>
                    <div class="track-left">
                        <div class="boxes">
                            @foreach($pad($utr ?? '', 10) as $c)
                                <span class="box">{!! $chr($c) !!}</span>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="field">
                    <span class="label">Verification Number</span>
                    <div class="track-left">
                        <div class="boxes">
                            <span class="box">V</span>
                            @foreach($pad($verification_no_left ?? '', 10) as $c)
                                <span class="box">{!! $chr($c) !!}</span>
                            @endforeach
                            <span class="box-slash">/</span>
                            @foreach($pad($verification_no_right ?? '', 2) as $c)
                                <span class="box">{!! $chr($c) !!}</span>
                            @endforeach
                        </div>
                    </div>
                    <div class="hint">Verification number only to be entered where a deduction at the higher rate has been made.</div>
                </div>
            </div>
            <div class="col col-right">
                @foreach($amountRows as $row)
                    @php [$L, $R] = $moneyToBoxes($row['value'] ?? 0); @endphp
                    <div class="amount-row">
                        <div class="amount-label">{!! !empty($row['strong']) ? '<strong>'.$row['label'].'</strong>' : e($row['label']) !!}</div>
                        <div class="amount-value">
                            <div class="boxes">
                                <span class="box box--currency">$</span>
                                @foreach($L as $c) <span class="box">{!! $chr($c) !!}</span> @endforeach
                                <span class="box-slash">.</span>
                                @foreach($R as $c) <span class="box">{!! $chr($c) !!}</span> @endforeach
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
 
    <div class="footer-note">Subcontractors - Please keep this document safe</div>
</div>
</body>
</html>
 