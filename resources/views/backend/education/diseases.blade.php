@extends('backend.layouts.default')

@section('page_title', 'Education diseases')

@section('style')
    <style>
        #uploadTrigger:hover {
            background-color: #f0f0f0;
            cursor: pointer;
        }

        .image-preview {
            max-width: 100px;
            max-height: 100px;
            margin-right: 1px;
        }
    </style>
@stop

@section('content')
    <div class="d-flex justify-content-start align-items-center m-3">
        <a href="{{ route('education.index') }}" class="text-secondary mr-2 mt-1">
            <i class="fa fa-arrow-left" style="font-size: 20px"></i>
        </a>
        <h2 class="h4"> {{ $diseases->title }}: diseases and images</h2>
    </div>
    <div class="card bradius p-2">
        <h6 class="ml-3">Diseases information:</h6>
        <form method="POST" action="{{ route('education.store') }}" enctype="multipart/form-data" id="educationForm">
            @csrf
            <input type="hidden" id="id" name="id" value="{{ $diseases->id }}">
            <textarea class="bradius" name="diseases" cols="30" rows="10"
                      style="width: 98%; margin: 15px">{{ $diseases->diseases }}</textarea>
            <div class="ml-3 mb-3">
                <h6>Images:</h6>
                <div class="d-flex flex-wrap" id="imagePreviewContainer">
                    @foreach ($diseases_images as $image)
                        <div class="p-2 mx-2">
                            <img src="{{ asset('storage/' . $image->url) }}" alt="Image" class="image-preview bradius">
                            <i class="fa fa-times-circle position-absolute top-0 end-0"
                               style="font-size: 20px; color: red; cursor: pointer;"
                               onclick="removeImage(this,{{ $image->id }},{{ $diseases->id }})"></i>
                        </div>
                    @endforeach
                </div>
                <div class="p-2">
                    <div class="border bg-light d-flex justify-content-center align-items-center bradius"
                         style="width: 100px; height: 100px; cursor: pointer;" id="uploadTrigger">
                        <span>+</span>
                        <input type="file" name="images[]" id="fileInput" style="display: none;" multiple/>
                    </div>
                </div>
            </div>
            <div class="d-grid">
                <button class="btn btn-success bradius float-right" type="submit" id="saveButton">Save</button>
            </div>
        </form>
    </div>
    <div class="d-grid">
        <!-- Loading Spinner Container -->
        <div id="loadingSpinnerContainer"
             style="display: none; align-items: center; justify-content: center; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0, 0, 0, 0.5); z-index: 1050;">
            <div id="loadingSpinner">
                <img src="{{ asset('images/200.webp') }}" alt="Loading" style="border-radius: 100px"/>
            </div>
        </div>

    </div>
    <script>
        function showSpinner() {
            const spinnerContainer = document.getElementById('loadingSpinnerContainer');
            spinnerContainer.style.display = 'flex'; // Show the spinner container
            spinnerContainer.style.zIndex = 1050; // Bring it to the front
        }

        // Function to hide the spinner
        function hideSpinner() {
            const spinnerContainer = document.getElementById('loadingSpinnerContainer');
            spinnerContainer.style.display = 'none'; // Hide the spinner container
            spinnerContainer.style.zIndex = -1; // Send it to the back
        }

        const uploadTrigger = document.getElementById('uploadTrigger');
        const fileInput = document.getElementById('fileInput');
        const form = document.getElementById('educationForm');

        uploadTrigger.addEventListener('click', function () {
            fileInput.click();
        });

        let uploadedFiles = []; // This will store file references

        function removeImage(index, imageId = null, infoID) {
            if (imageId) {
                var type = 'diseases';
                fetch(`/delete-image`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({imageId, infoID, type}),
                    credentials: 'same-origin'
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.message) {
                            console.log('Image deleted successfully');
                            index.parentNode.remove();
                        }
                    })
                    .catch(error => console.error('Error:', error));
            } else {
                // New image, just remove from DOM
                index.parentNode.remove();
                uploadedFiles.splice(index, 1); // Remove from the local array
            }
        }

        function displayImagePreview(file) {
            const reader = new FileReader();
            reader.onload = function (e) {
                const imagePreviewContainer = document.getElementById('imagePreviewContainer');
                const div = document.createElement('div');
                div.classList.add('position-relative', 'd-inline-block', 'p-2', 'mx-2');

                const imgElement = document.createElement('img');
                imgElement.classList.add('image-preview', 'bradius');
                imgElement.src = e.target.result;

                const iClose = document.createElement('i');
                iClose.classList.add('fa', 'fa-times-circle', 'position-absolute', 'top-0', 'end-0');
                iClose.style.fontSize = '20px';
                iClose.style.cursor = 'pointer';
                iClose.style.color = 'red';
                iClose.onclick = function () {
                    removeImage(iClose)
                };

                div.appendChild(imgElement);
                div.appendChild(iClose);
                imagePreviewContainer.appendChild(div);
            };
            reader.readAsDataURL(file);
        }

        fileInput.addEventListener('change', function (event) {
            const files = event.target.files;
            for (let i = 0; i < files.length; i++) {
                uploadedFiles.push(files[i]); // Add new files to array
                displayImagePreview(files[i]);
            }
        });

        form.addEventListener('submit', function (e) {
            e.preventDefault();
            // showSpinner(); // Call to show the spinner

            const formData = new FormData();
            uploadedFiles.forEach((file) => {
                formData.append('images[]', file);
            });

            // Include other form data
            formData.append('diseases', document.querySelector('[name="diseases"]').value);
            formData.append('id', document.querySelector('[id="id"]').value); // Assuming 'id' is always present

            // Submit form data using fetch or similar (adjust URL as necessary)
            fetch('{{ route('education.store') }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'), // Add CSRF Token
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest' // Indicates an AJAX request
                },
                body: formData,
                credentials: 'same-origin' // Ensure cookies (including CSRF token cookie) are sent with the request
            })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json(); // or response.text() if the response is not JSON
                })
                .then(data => {
                    // If you expect a specific property in data for a successful operation
                    // e.g., data.success or data.redirectUrl
                    window.location.href = data.redirectUrl; // Redirect to a success page or URL provided by the server
                })
                .catch((error) => {
                    console.error('Error:', error);
                    // Optionally implement error handling logic here
                });
        });
    </script>
@stop
