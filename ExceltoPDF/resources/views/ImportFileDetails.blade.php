<div class="mb-6 flex items-center space-x-4">
    <!-- Generate PDF (Scheduler trigger) -->
    <!-- <button class="generate-pdf-btn flex items-center space-x-2 px-3 py-2 bg-green-600 text-white rounded-md shadow-sm hover:bg-green-700 transition-all duration-200"
            data-id="{{ $subcontractors[0]->contractor_id }}"
            title="Generate PDF for Contractor">
        <i data-feather="file-text" class="w-4 h-4"></i>
        <span>Generate PDF</span>
    </button> -->

     <form action="{{ route('download.zip') }}" method="POST" class="inline">
        @csrf
        <input type="hidden" name="subcontractor_ids" value="{{ $subcontractors->pluck('unique_id')->implode(',') }}">

        <button type="submit"
                class="flex items-center space-x-2 px-3 py-2 bg-yellow-600 text-white rounded-md shadow-sm hover:bg-yellow-700 transition-all duration-200"
                title="Download All as ZIP">
            <i data-feather="archive" class="w-4 h-4"></i>
            <span>Download ZIP</span>
        </button>
    </form>
</div>

<!-- Data Table -->
<div class="overflow-x-auto" data-contractor-id="{{ $subcontractors[0]->contractor_id}}" id="subcontractor-table">
    <table class="w-full border border-gray-200 rounded-lg shadow-sm">
        <thead>
            <tr class="bg-gray-100 text-gray-700 text-sm">
                <th class="px-4 py-3 border-b border-gray-300 text-left">Forename</th>
                <th class="px-4 py-3 border-b border-gray-300 text-left">Surname</th>
                <th class="px-4 py-3 border-b border-gray-300 text-left">UTR</th>
                <th class="px-4 py-3 border-b border-gray-300 text-left">Verification Number</th>
                <th class="px-4 py-3 border-b border-gray-300 text-center">Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($subcontractors as $sub)
                <tr class="hover:bg-gray-50 text-sm transition">
                    <td class="px-4 py-2 border-b border-gray-200">{{ $sub->forename }}</td>
                    <td class="px-4 py-2 border-b border-gray-200">{{ $sub->surname }}</td>
                    <td class="px-4 py-2 border-b border-gray-200">{{ $sub->utr }}</td>
                    <td class="px-4 py-2 border-b border-gray-200">{{ $sub->verification_number }}</td>
                    <td class="px-4 py-2 border-b border-gray-200 text-center">
                        <form action="{{ route('download.pdf') }}" method="POST" class="inline">
                            @csrf
                            <input type="hidden" name="unique_id" value="{{ $sub->unique_id }}">
                            <button type="submit"
                                    class="p-2 bg-blue-600 text-white rounded-md shadow-sm hover:bg-blue-700 transition"
                                    title="Download PDF">
                                <i data-feather="download" class="w-4 h-4"></i>
                            </button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
 <div class="mt-4">
    <ul class="pagination">
        {{ $subcontractors->links() }}
    </ul>
</div>

</div>

<script>
    // Refresh feather icons after content loads
    feather.replace();

    // Generate PDF button handler
    // document.querySelectorAll('.generate-pdf-btn').forEach(btn => {
    //     btn.addEventListener('click', function() {
    //         const contractorId = Number(this.dataset.id);
    //         fetch("{{ route('pdf.queue') }}", {
    //             method: 'POST',
    //             headers: {
    //                 'X-CSRF-TOKEN': '{{ csrf_token() }}',
    //                 'Content-Type': 'application/json'
    //             },
    //             body: JSON.stringify({ contractor_id: contractorId })
    //         })
    //         .then(res => res.json())
    //         .then(data => {
    //             alert(data.message);
    //         })
    //         .catch(err => console.error(err));
    //     });
    // });
</script>
