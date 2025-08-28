<table class="w-full border border-gray-300 rounded-lg shadow-md mt-6">
    @foreach($rows[0]['data'] as $row)
        @foreach($row as $row_content)
            @if($loop->first)
                <tr class="bg-gray-100 text-gray-700">
                    @foreach($row_content as $heading=>$item)
                        <th class="px-4 py-3 border-b border-gray-300 text-left font-semibold">
                            {{ucfirst(str_replace('_', ' ',$heading))}}
                        </th>
                    @endforeach
                    <th class="px-4 py-3 border-b border-gray-300 text-center font-semibold">
                        Action
                    </th>
                </tr>
            @endif

            <tr class="hover:bg-gray-50 transition">
                @foreach($row_content as $heading=>$item)
                    <td class="px-4 py-2 border-b border-gray-200">
                        {{$item}}
                    </td>
                @endforeach
                <td class="px-4 py-2 border-b border-gray-200 text-center">
                    <form action="{{ route('download.pdf') }}" method="POST">
                        @csrf
                        <input type="hidden" name="{{ $heading }}" value="{{ $item }}">
                        <button
                            type="submit"
                            class="px-4 py-1 text-white bg-blue-600 rounded-md
                                   hover:bg-blue-700 shadow-sm transition-all duration-200
                                   cursor-pointer hover:scale-105"
                        >
                            Download PDF
                        </button>
                    </form>
                </td>
            </tr>
        @endforeach
    @endforeach
</table>
