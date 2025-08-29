<html>
  <head>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  </head>
  <body class="bg-gray-100 flex justify-center w-[100%] min-h-screen pt-16">
    <div class="w-[90%]  bg-white shadow-lg rounded-md p-8">
      <h2 class="text-2xl font-bold text-gray-800 mb-6 text-center">Upload Excel File</h2>

      
      <form id="excel-upload-form" enctype="multipart/form-data" class="space-y-6">
        @csrf
        <div class="flex justify-center">
          <input 
            type="file" 
            name="import_file" 
            accept=".csv,.xls,.xlsx" 
            required
            class=" text-sm border border-gray-300 rounded-md cursor-pointer bg-gray-50 focus:outline-none p-3"
          >
        </div>
        <div class="flex justify-center">
          <button 
            type="submit" 
            class="px-6 py-2 text-white bg-blue-600 rounded-md hover:bg-blue-700 focus:ring-blue-500 shadow-md transition-all duration-200"
          >
            Upload
          </button>
        </div>
      </form>

      <div id="preview-table" class="mt-8 overflow-x-auto overflow-y-auto h-100"></div>
    </div>

    <script>
      $('#excel-upload-form').on('submit', function(e) {
        e.preventDefault();
        let formData = new FormData(this);

        $.ajax({
          url: "{{ route('excel.import') }}",
          type: "POST",
          data: formData,
          contentType: false,
          processData: false,
          success: function(response) {
            $('#preview-table').html(response.html);
          },
          error: function() {
            $('#preview-table').html(
              `<p class="text-red-500 text-center">Upload failed. Please try again.</p>`
            );
          }
        });
      });
    </script>
  </body>
</html>
