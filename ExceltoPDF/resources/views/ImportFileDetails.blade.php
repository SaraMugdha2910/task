<table>
    <tr>
        @foreach($rows as $row)
            <tr>
                @foreach ($row as $conent)
                    <td>
                        {{$conent}}
                    </td>
                @endforeach
                <td>
                    @if (!$loop->first) 
                        <button>Download PDF</button>
                    @endif
                </td>
            </tr>
        @endforeach
    </tr>
</table>