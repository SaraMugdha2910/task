<table>
    <tr>
        @foreach($rows[0]['data'] as $row)
            <tr>
                @foreach ($row[0] as $content)
                    <td>
                        {{$content}}
                    </td>
                @endforeach
                <td>
                    @if (!$loop->first) 
                         <form action="{{ route('download.pdf') }}" method="POST">
                        @csrf
                        @foreach($row as $key => $value)
                            <input type="hidden" name="row[{{ $key }}]" value="{{ $value }}">
                        @endforeach
                        <button type="submit">Download PDF</button>
                    </form>
                    @endif
                </td>
            </tr>
        @endforeach
    </tr>
</table>