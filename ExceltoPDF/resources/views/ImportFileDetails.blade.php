@php
    $filteredRows = collect($rows[0]['data'])->map(function($row) {
        return collect($row)->filter(function($row_content) {
            return collect($row_content)->filter(function($value) {
                return !empty($value);
            })->isNotEmpty();
        });
    })->filter(function($row) {
        return $row->isNotEmpty();
    })->values()->toArray();
@endphp

<div>
    <form action="{{ route('download.zip') }}" method="POST">
        @csrf
        <button class="px-4 py-1 text-white bg-yellow-600 rounded-md
                       hover:bg-yellow-700 shadow-sm transition-all duration-200
                       cursor-pointer hover:scale-105"
                type="submit">
            <input type="hidden" value="{{ json_encode($filteredRows) }}" name="row_data">
            <input type="hidden" name="header_data" value="{{ json_encode($rows[0]['header']) }}">
            Download as ZIP
        </button>
    </form>
</div>

<table class="w-full border table-auto border-gray-300 rounded-lg shadow-md mt-6">
    @foreach($rows[0]['data'] as $row)
        @foreach($row as $row_content)
            @if($loop->first)
                <tr class="bg-gray-100 text-gray-700">
                    @foreach($row_content as $heading => $item)
                        <th class="px-4 py-3 border-b border-gray-300 text-left font-semibold">
                            {{ucfirst(str_replace('_', ' ', $heading))}}
                        </th>
                    @endforeach
                    <th class="px-4 py-3 border-b border-gray-300 text-center font-semibold">
                        Action
                    </th>
                </tr>
            @endif
            @php
                $hasData = collect($row_content)->filter(function($value) {
                    return !empty($value);
                })->isNotEmpty();
            @endphp
            @if($hasData)
                <tr class="hover:bg-gray-50 transition">
                    @foreach($row_content as $heading => $item)
                        <td class="px-4 py-2 border-b border-gray-200">
                            {{$item}}
                        </td>
                    @endforeach
                    <td class="px-4 py-2 border-b border-gray-200 text-center">
                        <form action="{{ route('download.pdf') }}" method="POST">
                            @csrf

                            <input type="hidden" name="payload"
                                value='{{ json_encode(array_merge($row_content, $rows[0]['header'][0])) }}'>

                            <button type="submit" class="px-4 py-1 text-white bg-blue-600 rounded-md
                                    hover:bg-blue-700 shadow-sm transition-all duration-200
                                    cursor-pointer hover:scale-105">
                                Download PDF
                            </button>
                        </form>
                    </td>
                </tr>
            @endif
        @endforeach
    @endforeach
</table>