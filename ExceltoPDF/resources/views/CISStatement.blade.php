<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>CIS Statement</title>
    <style>
        @page {
            size: A4;
            margin: 10mm 15mm 10mm 15mm;
        }

        html,
        body {
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11px;
            color: #0f3e46;
            line-height: 1.25;
        }

        .mb {
            margin-bottom: 25px
        }

        .teal {
            color: #009290;
        }

        .header-title {
            font-size: 18px;
            font-weight: 700;
            color: #009290;
        }

        .rule {
            height: 10px;
            background: #009290;
            margin: 3mm 0 7mm 0;
            margin-bottom: 7mm;
        }

        .panel {
            border: 1px solid #92c8cf;
            padding: 5mm 5mm 4mm 8mm;
            margin-bottom: 10mm;
            page-break-inside: avoid;
        }

        .panel-title {
            font-weight: 700;
            color: #157c96;
            font-size: 14px;
        }

        .grid {
            display: table;
            width: 100%;
            table-layout: fixed;
        }

        .col {
            display: table-cell;
            vertical-align: top;
        }

        .col-left {
            width: 55%;
            padding-right: 4mm;
        }

        .col-right {
            width: 45%;
            padding-left: 4mm;
        }

        .field {
            margin: 2.5mm 0;
            margin-bottom: 15mm;
        }

        .label {
            display: block;
            color: black;
            font-weight: 900;
            font-size: 10px;
            margin: 0 0 1mm 0;
        }

        .hint {
            /* color: #6da6ad; */
            font-size: 10px;
            margin-bottom: 1mm;
        }

        .track-left {
            display: inline-block;
            width: 73mm;
        }

        .line-input {
            border: 1px solid #a8d7d7;
            height: 5mm;
        }

        .line-input.multiline {
            height: 18mm;
        }

        .boxes {
            display: inline-block;
            white-space: nowrap;
        }

        .box {
            display: inline-block;
            width: 4mm;
            height: 5mm;
            border: 1px solid #a8d7d7;
            text-align: center;
            vertical-align: middle;
            line-height: 5mm;
            font-size: 12px;
            font-weight: 700;
            color: #333;
            margin-right: 0.3mm;
            box-sizing: border-box;
            overflow: hidden;
            text-overflow: clip;
            white-space: nowrap;
        }

        .box:last-child {
            margin-right: 0;
        }

        .box--currency {
            background: #fff;
            color: #a8d7d7;
            border-color: 1px solid #a8d7d7;
            font-weight: 700;
        }

        .box-slash {
            color: #a8d7d7;
            font-weight: 700;
            margin: 0 1mm;
            font-size: 20px;
            justify-self: center;
            align-items: center;
        }

        .amount-row {
            margin: 3mm 0;
            margin-bottom: 25px;
        }

        .amount-label {
            display: block;
            width: 100%;
            margin-bottom: 2.5mm;
        }

        .amount-value {
            display: block;
            width: 100%;
            text-align: left;
        }

        .amount-value .boxes {
            padding-left: 2mm;
        }

        .muted {
            color: #6da6ad;
        }

        .footer-note {
            text-align: center;
            margin-top: 5mm;
            margin-bottom: 4mm;
            color: black;
            font-size: 12px;
            page-break-inside: avoid;
            font-weight: 600;
        }

        .no-break {
            page-break-inside: avoid;
            page-break-before: auto;
            page-break-after: auto;
        }
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
                $s = (string) ($value ?? '');
                $s = preg_replace('/\s+/', '', $s);
                return str_split(str_pad($s, $length, ' ', STR_PAD_RIGHT));
            };
            $moneyToBoxes = function ($value, $digits = 6) {
                $num = number_format((float) ($value ?? 0), 2, '.', '');
                $num = str_replace(['£', ','], '', $num);
                $parts = explode('.', $num);
                $left = preg_replace('/\D/', '', $parts[0]);
                $left = str_pad($left, $digits, ' ', STR_PAD_LEFT);
                $right = str_pad($parts[1] ?? '00', 2, '0', STR_PAD_RIGHT);
                return [str_split($left), str_split($right)];
            };

            $chr = function ($c) {
                return $c === ' ' || $c === '' ? '&nbsp;' : e($c);
            };

            $taxInput = $period_end ?? '';
            $day = '';
            $month = '';
            $year = '';
            if (!empty($taxInput)) {
                $ts = strtotime($taxInput);

                if ($ts) {
                    $day = date('d', $ts);
                    $month = date('m', $ts);
                    $year = date('Y', $ts);
                } else {
                    $digits = preg_replace('/\D+/', '', $taxInput);

                    if (strlen($digits) >= 8) {
                        $day = substr($digits, 0, 2);
                        $month = substr($digits, 2, 2);
                        $year = substr($digits, -4);
                    } elseif (strlen($digits) >= 6) {
                        $day = '01';
                        $month = substr($digits, 0, 2);
                        $year = substr($digits, -4);
                    }

                    $month = str_pad((string) intval($month), 2, '0', STR_PAD_LEFT);
                }
            }

            $taxBoxes = $day . $month . $year;


            $amountRows = $amount_rows ?? [
                ['label' => 'Gross amount paid (Excl VAT) (A)', 'value' => ($total_payments ?? 0)],
                ['label' => 'Less cost of materials', 'value' => ($cost_of_materials ?? 0)],
                ['label' => 'Amount liable to deduction', 'value' => ($liable_amount ?? 0)],
                ['label' => 'Amount deducted (B)', 'value' => ($total_deducted ?? 0)],
                ['label' => 'Amount payable (A - B)', 'value' => ($total_payments - $total_deducted ?? 0), 'strong' => true],
            ];


            $ver = $verification_number ?? '';
            $ver = ltrim($ver, 'V');
            [$verification_no_left, $verification_no_right] = array_pad(explode('/', $ver, 2), 2, '');



            $empRef = $aoref ?? '';

            $emp_ref_left = substr($empRef, 0, 3);
            $emp_ref_right = substr($empRef, 3, 8);   




        @endphp

        <div class="panel">
            <div class="panel-title">Contractor details</div>
            <div class="grid">
                <div class="col col-left">
                    <div class="field">
                        <span class="mb">Contractor’s name</span>
                        <div class="track-left">
                            <div class="line-input" style="margin-top: 15px;"></div>
                            <div class="line-input"></div>
                        </div>
                    </div>
                    <div class="field">
                        <span class="mb">Contractor’s address</span>
                        <div class="track-left">
                                <div class="line-input" style="margin-top: 15px;"></div>
                                <div class="line-input"></div>
                                <div class="line-input"></div>
                        </div>
                    </div>
                </div>
                <div class="col col-right">
                    <div class="field">
                        <span class="label">Payment and deduction made in tax month ended</span>
                        <div class="label">{{ $day}} MM YYYY</div>
                        <div class="track-left">
                            <div class="boxes" style="margin-top: 15px;">
                                @foreach($pad($taxBoxes, 8) as $c)
                                    <span class="box">{!! $chr($c) !!}</span>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <div class="field" style="margin-top:5mm;">
                        <span class="mb">Employer’s Tax Reference</span>
                        <div class="track-left">
                            <div class="boxes" style="margin-top: 15px;">
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
                        <span class="mb">Subcontractor’s full name</span>
                        <div class="track-left">
                            <div class="line-input" style="margin-top: 15px;">{{" ". $title }} {{ $forename }}</div>
                            <div class="line-input"> {{" ". $surname }}</div>
                        </div>
                    </div>
                    <div class="field">
                        <span class="mb">Unique Taxpayer reference (UTR)</span>
                        <div class="track-left">
                            <div class="boxes" style="margin-top: 15px;">
                                @foreach($pad($sub_contractor_utr ?? '', 10) as $c)
                                    <span class="box">{!! $chr($c) !!}</span>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <div class="field">
                        <span class="mb">Verification Number*</span>
                        <div class="track-left">
                            <div class="boxes" style="margin-top: 15px;">
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
                        <br>
                        <div class="hint" style="margin-top: 20px;">* Verification number only to be entered where a
                            deduction at the higher rate
                            has been made.</div>
                    </div>
                </div>
                <div class="col col-right">
                    @foreach($amountRows as $row)
                        @php [$L, $R] = $moneyToBoxes($row['value'] ?? 0); @endphp
                        <div class="amount-row">
                            <div class="amount-label">
                                {!! !empty($row['strong']) ? $row['label'] : e($row['label']) !!}
                            </div>
                            <div class="amount-value">
                                <div class="boxes">
                                    <span class="box box--currency">£</span>
                                    @foreach($L as $c) <span class="box">{!! $chr($c) !!}</span> @endforeach
                                    <span class="box-slash " style="color:black !important; font-size: 12px; align-items: center;">.</span>
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

    <footer style="width: 100%; margin-top: 55px;">
        <div style="display: table; width: 100%;">
            <div style="display: table-row;">
                <span style="display: table-cell; text-align: left;">{{ $works_ref }}</span>
                <span style="display: table-cell; text-align: right;">HMRC 09/08</span>
            </div>
        </div>
    </footer>

</body>

</html>