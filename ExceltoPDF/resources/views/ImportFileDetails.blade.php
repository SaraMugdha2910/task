<div class="mb-4">
    <form action="{{ route('download.zip') }}" method="POST">
        @csrf
        <input type="hidden" name="subcontractor_ids" value="{{ $subcontractors->pluck('unique_id')->implode(',') }}">
        <button type="submit"
                class="px-4 py-2 bg-yellow-600 text-white rounded-md
                       hover:bg-yellow-700 shadow-sm transition-all duration-200
                       cursor-pointer hover:scale-105">
            Download All as ZIP
        </button>
    </form>
</div>

<table class="w-full border table-auto border-gray-300 rounded-lg shadow-md mt-6">
    <thead>
        <tr class="bg-gray-100 text-gray-700">
            <th class="px-4 py-3 border-b border-gray-300">Forename</th>
            <th class="px-4 py-3 border-b border-gray-300">Surname</th>
            <th class="px-4 py-3 border-b border-gray-300">UTR</th>
            <th class="px-4 py-3 border-b border-gray-300">Verification Number</th>
            <th class="px-4 py-3 border-b border-gray-300 text-center">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($subcontractors as $sub)
        <tr class="hover:bg-gray-50 transition">
            <td class="px-4 py-2 border-b border-gray-200">{{ $sub->forename }}</td>
            <td class="px-4 py-2 border-b border-gray-200">{{ $sub->surname }}</td>
            <td class="px-4 py-2 border-b border-gray-200">{{ $sub->utr }}</td>
            <td class="px-4 py-2 border-b border-gray-200">{{ $sub->verification_number }}</td>
            <td class="px-4 py-2 border-b border-gray-200 text-center">
                <form action="{{ route('download.pdf') }}" method="POST">
                    @csrf
                    <input type="hidden" name="unique_id" value="{{ $sub->unique_id }}">
                    <button type="submit"
                            class="cursor-pointer px-4 py-1 bg-blue-600 text-white rounded-md
                                   hover:bg-blue-700 shadow-sm transition-all duration-200">
                        Download PDF
                    </button>
                </form>
            </td>
        </tr>
    @endforeach
    </tbody>
</table>
