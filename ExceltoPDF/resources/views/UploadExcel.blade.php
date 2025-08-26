<body>
    <div class="container">
    <form action="{{route('excel.import')}}" method="POST"  enctype="multipart/form-data">
        @csrf
        <input type="file" name="import_file" accept=".csv,.xls,.xlsx">
        <button type="submit">Submit</button>
    </form>
    </div>
</body>