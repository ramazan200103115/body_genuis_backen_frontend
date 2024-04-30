@extends('backend.layouts.default')

@section('page_title', 'Create Quiz')

@section('style')
    <style>
        #uploadTrigger:hover {
            background-color: #f0f0f0;
            cursor: pointer;
        }

        .image-preview {
            max-width: 100px;
            max-height: 100px;
            margin-right: 2px;
        }
    </style>
@stop

@section('content')
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12">
                <div class="d-flex justify-content-start align-items-center m-3">
                    <a href="{{ route('quiz.index') }}" class="text-secondary mr-2">
                        <i class="fa fa-solid fa-arrow-left"></i>
                    </a>
                    <h2 class="h4"> Creating a new quiz</h2>
                </div>
            <div class="card bradius">
                <div class="card-body">
                    @include('backend.partials.error')

                    <form method="POST" action="{{ route('quiz.store') }}" enctype="multipart/form-data" id="quizForm">
                        <div class="mb-3">
                            <label for="inputName" class="form-label">Name:</label>
                            <input type="text" name="name" class="form-control" id="inputName"
                                   placeholder="Enter name" required>
                        </div>
                        {{ csrf_field() }}

                        <label for="images">Logo:</label>
                        <div class="d-flex flex-wrap" id="imagePreviewContainer">
                            <div class="position-relative">
                            </div>
                        </div>
                        <div class="p-2">
                            <div class="border bg-light d-flex justify-content-center align-items-center bradius"
                                 style="width: 90px; height: 90px; cursor: pointer;" id="uploadTrigger">
                                <span style="font-size: 60px">+</span>
                                <input type="file" name="images" id="fileInput" style="display: none;"/>
                            </div>
                        </div>
                        <button id="saveButton" type="submit" class="btn btn-success bradius float-right">Save</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop
<script>
    function removeImage(element) {
        // This will remove the image container div
        element.parentNode.remove();
    }

    document.addEventListener('DOMContentLoaded', function () {
        // Trigger file input when "+" icon is clicked
        document.getElementById('uploadTrigger').addEventListener('click', function () {
            document.getElementById('fileInput').click();
        });

        // Submit form when save button is clicked
        document.getElementById('saveButton').addEventListener('click', function () {
            document.getElementById('questionForm').submit();
        });

        let uploadedFiles = []; // Array to store uploaded files

        // Function to display uploaded images as thumbnails
        function displayImagePreview(file) {
            const reader = new FileReader();
            reader.onload = function (e) {
                const imagePreviewContainer = document.getElementById('imagePreviewContainer');
                imagePreviewContainer.innerHTML = ''; // Clear the container
                const div = document.createElement('div');
                div.classList.add('position-relative');

                const imgElement = document.createElement('img');
                imgElement.classList.add('image-preview');
                imgElement.src = e.target.result;

                const iClose = document.createElement('i');
                iClose.classList.add('fa', 'fa-times-circle', 'position-absolute', 'top-0', 'end-0');
                iClose.setAttribute('aria-hidden', 'true');
                iClose.onclick = function () {
                    removeImage(iClose);
                };
                iClose.style.fontSize = '20px';
                iClose.style.cursor = 'pointer';
                iClose.style.color = 'red';

                div.appendChild(imgElement);
                div.appendChild(iClose);
                imagePreviewContainer.appendChild(div);
            };
            reader.readAsDataURL(file);
        }


        // Handle file input change event
        document.getElementById('fileInput').addEventListener('change', function (event) {
            const file = event.target.files[0]; // Get the first file only
            if (file) {
                uploadedFile = file; // Update the uploadedFile variable
                displayImagePreview(file);
            }
        });
    });
</script>
