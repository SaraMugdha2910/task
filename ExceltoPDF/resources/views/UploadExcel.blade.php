<html>
  <head>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
  </head>
  <body class="bg-gray-100 flex items-center justify-center min-h-screen">
    <div class="w-full max-w-md bg-white shadow-lg rounded-md p-8">
      <h2 class="text-2xl font-bold text-gray-800 mb-6 text-center">Upload Excel File</h2>

      <form action="{{route('excel.import')}}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf

        <!-- File Input -->
        <div>
          <input 
            type="file" 
            name="import_file" 
            accept=".csv,.xls,.xlsx" 
            required
            class="w-full text-sm border border-gray-300 rounded-md cursor-pointer bg-gray-50 focus:outline-none"
          >
        </div>

        <!-- Submit Button -->
        <div class="flex justify-center">
          <button 
            type="submit" 
            class="px-6 py-2 text-white bg-blue-600 rounded-md hover:bg-blue-700 focus:ring-blue-500 shadow-md transition-all duration-200"
          >
            Upload
          </button>
        </div>
      </form>
    </div>
  </body>
</html>
