<style>
    .form-check {
        width: 700px;
        margin-top: 10px;
    }

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
<form method="POST" action="{{ route('save.question', $quiz->id) }}" enctype="multipart/form-data" id="questionForm">

    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <label class="control-label col-md-2 col-sm-2 col-xs-12">Question:</label>
                <div class="col-12">
                    <input type="text" name="question" required="required" placeholder="Enter your question"
                           class="form-control bradius" value="{{$question->question ?? ''}}">
                </div>
            </div>
        </div>
    </div>
    <div>
        @empty($question)
            <div class="form-check">
                <input class="form-check-input" type="radio" name="answerOptions" id="option1" value="0" checked>
                <input type="text" name="options[]" placeholder="Write option answer" class="form-control bradius">
            </div>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="answerOptions" id="option2" value="1">
                <input type="text" name="options[]" placeholder="Write option answer" class="form-control bradius">
            </div>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="answerOptions" id="option3" value="2">
                <input type="text" name="options[]" placeholder="Write option answer" class="form-control bradius">
            </div>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="answerOptions" id="option4" value="3">
                <input type="text" name="options[]" placeholder="Write option answer" class="form-control bradius">
            </div>
        @else
            @foreach($question->options as $key => $option)
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="answerOptions" id="option{{$key}}"
                           value="{{ $key }}" {{ $option->is_right_option === 1 ? 'checked' : '' }}>
                    <label style="width: 100%" for="option{{$key}}">
                        <input type="text" name="options[{{ $key }}]" placeholder="Write option answer"
                               class="form-control bradius"
                               value="{{$option->option}}">
                    </label>
                </div>
            @endforeach

        @endif
    </div>
    <div class="m-3">
        <label for="images">Image:</label>
        <div class="d-flex flex-wrap" id="imagePreviewContainer">
            @if(!empty($question->image_url))
                <div class="position-relative">
                    <img src="{{ $question->image_url }}" class="image-preview">
                    <i class="fa fa-times-circle position-absolute top-0 end-0" aria-hidden="true"
                        onclick="removeImage(this,{{$question->id}})"
                        style="font-size: 20px; color: red"></i>
                </div>
            @endif
        </div>
        <div class="p-2">
            <div class="border bg-light d-flex justify-content-center align-items-center bradius"
                 style="width: 90px; height: 90px; cursor: pointer;" id="uploadTrigger">
                <span style="font-size: 60px">+</span>
                <input type="file" name="images" id="fileInput" style="display: none;"/>
            </div>
        </div>
    </div>
    @empty($question)
        <input type="hidden" name="id" value="0">
    @else
        <input type="hidden" name="id" value="{{$question->id}}">
    @endempty
    {{ csrf_field() }}
    <div class="d-grid">
        <button class="btn btn-success bradius float-right" type="button" id="saveButton">Save</button>
    </div>
</form>
<script>
    function removeImage(index, questionId = null) {
        if (questionId) {
            fetch(`/delete-question-image/${questionId}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
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
            index.parentNode.remove();
        }
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
                iClose.onclick = function() { removeImage(iClose); };
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
