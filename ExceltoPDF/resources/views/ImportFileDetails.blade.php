<table>
    @foreach($rows[0]['data'] as $row)
        @foreach($row as $row_content)
        @if($loop->first)
            @foreach($row_content as $heading=>$item)
                <th>
                    {{ucfirst(str_replace('_', ' ',$heading))}}
                </th>
            @endforeach
        @endif
            <tr>
                @foreach($row_content as $heading=>$item)
                    <td>
                        {{$item}}
                    </td>
                @endforeach
            </tr>
        @endforeach
    @endforeach
</table>