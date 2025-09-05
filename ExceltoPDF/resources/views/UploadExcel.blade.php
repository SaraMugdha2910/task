<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CIS Statement Upload</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://unpkg.com/feather-icons"></script>
  
<style>
.spinner {
   position: relative;
   width: 15.7px;
   height: 15.7px;
}

#spinner-overlay{
  background-color: rgba(129, 127, 127, 0.47);
}

.spinner div {
   animation: spinner-4t3wzl 2.25s infinite backwards;
   background-color: #ffffffff;
   border-radius: 50%;
   height: 100%;
   position: absolute;
   width: 100%;
}

.spinner div:nth-child(1) {
   animation-delay: 0.18s;
   background-color: rgba(255, 255, 255, 0.9);
}

.spinner div:nth-child(2) {
   animation-delay: 0.36s;
   background-color: rgba(255, 255, 255, 0.8);
}

.spinner div:nth-child(3) {
   animation-delay: 0.54s;
   background-color: rgba(255, 255, 255, 1);
}

.spinner div:nth-child(4) {
   animation-delay: 0.72s;
   background-color: rgba(255, 255, 255, 1);
}

.spinner div:nth-child(5) {
   animation-delay: 0.8999999999999999s;
   background-color: rgba(255, 255, 255, 1);
}

@keyframes spinner-4t3wzl {
   0% {
      transform: rotate(0deg) translateY(-200%);
   }

   60%, 100% {
      transform: rotate(360deg) translateY(-200%);
   }
}
</style>
</head>
<body class="bg-gray-100 min-h-screen flex flex-col items-center">

  
<div id="spinner-overlay" class="hidden fixed inset-0  flex items-center justify-center z-50">
    <div class="spinner">
        <div></div>
        <div></div>
        <div></div>
        <div></div>
        <div></div>
    </div>
</div>



    <!-- Main Content -->
    <main class="flex-grow w-full flex justify-center mt-12">
        <div class="w-[80%] space-y-6">

            <!-- Upload Box -->
            <div class="bg-white shadow-md rounded-md p-6 flex flex-col items-center w-full">
                <h2 class="text-lg font-bold text-gray-800 mb-4">Upload CIS Statement Excel</h2>

                <form id="excel-upload-form" enctype="multipart/form-data" class="w-full space-y-4">
                    @csrf
                    <label class="flex flex-col items-center justify-center w-full h-24 border-2 border-dashed border-gray-300 rounded-md cursor-pointer hover:border-blue-400 hover:bg-blue-50 transition">
                        <i data-feather="upload-cloud" class="w-8 h-8 text-gray-400 mb-1"></i>
                        <span class="text-sm text-gray-600">Click to select file</span>
                        <input type="file" name="import_file" accept=".csv,.xls,.xlsx" class="hidden" required>
                    </label>

                    <!-- File Name -->
                    <p id="file-name" class="text-center text-gray-700 text-sm hidden"></p>

                    <div class="flex justify-center">
                        <button id="upload-btn" type="submit"
                                class="flex items-center space-x-2 px-6 py-2 text-white bg-blue-600 rounded-md hover:bg-blue-700 shadow-md transition-all duration-200 disabled:opacity-50"
                                disabled>
                            <span>Upload & Preview</span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Preview Table Box -->
            <div id="preview-table" class="bg-white shadow-md rounded-md overflow-x-auto hidden w-full"></div>

        </div>
    </main>

    <script>
        feather.replace();

        // Enable upload button and show file name
        $('input[name="import_file"]').on('change', function() {
            const file = this.files[0];
            $('#upload-btn').prop('disabled', !file);
            if (file) {
                $('#file-name').text(file.name).removeClass('hidden');
            } else {
                $('#file-name').text('').addClass('hidden');
            }
        });

        $('#excel-upload-form').on('submit', function(e) {
            e.preventDefault();
            let formData = new FormData(this);

            // Show spinner overlay
            $('#spinner-overlay').removeClass('hidden');

            $.ajax({
                url: "{{ route('excel.import') }}",
                type: "POST",
                data: formData,
                contentType: false,
                processData: false,
                success: function(response) {
                    $('#preview-table').removeClass('hidden').html(response.html);
                    feather.replace();
                },
                error: function() {
                    $('#preview-table').removeClass('hidden').html(
                        `<p class="text-red-500 text-center py-6">Upload failed. Please try again.</p>`
                    );
                },
                complete: function() {
                    $('#spinner-overlay').addClass('hidden');
                }
            });
        });
    </script>
</body>
</html>
