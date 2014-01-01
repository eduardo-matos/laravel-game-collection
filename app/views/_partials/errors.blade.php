@if($errors->any())
    <div class="errors-container">
        <p>Errors</p>
        <ul class="errors">
            {{ implode('', $errors->all('<li>:message</li>')) }}
        </ul>
    </div>
@endif
