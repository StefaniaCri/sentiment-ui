<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clasificator de Sentimente</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<meta name="csrf-token" content="{{ csrf_token() }}">
<body>
<div class="container mt-5">
    <form method="POST" action="{{ route('analyze') }}">
        @csrf

        <div class="mb-3">
            <label for="title" class="form-label">Introduceți titlul articolului:</label>
            <input
                type="text"
                id="title"
                name="title"
                class="form-control"
                placeholder="Scrieți titlul aici..."
                value="{{ old('title', $title ?? '') }}">
        </div>

        <div class="mb-3">
            <label for="link" class="form-label">Introduceți link-ul articolului:</label>
            <input
                type="url"
                id="link"
                name="link"
                class="form-control"
                placeholder="Scrieți link-ul aici..."
                value="{{ old('link', $link ?? '') }}">
            <button type="button" id="fetchTitle" class="btn btn-primary mt-2">Obține titlul articolului</button>
        </div>

        <div class="mb-3">
            <label for="model" class="form-label">Selectați modelul:</label>
            <select id="model" name="model" class="form-select">
                <option value="bert-base-spanish-wwm-uncased" {{ (old('model', $model ?? '') == 'bert-base-spanish-wwm-uncased') ? 'selected' : '' }}>BERT Base Spaniol (articol)</option>
                <option value="M47Labs/spanish_news_classification_headlines" {{ (old('model', $model ?? '') == 'M47Labs/spanish_news_classification_headlines') ? 'selected' : '' }}>BERT Spanish News Classification</option>
                <option value="dumitrescustefan/bert-base-romanian-cased-v1" {{ (old('model', $model ?? '') == 'dumitrescustefan/bert-base-romanian-cased-v1') ? 'selected' : '' }}>BERT Base Română</option>
                <option value="lucasresck/bert-base-cased-ag-news" {{ (old('model', $model ?? '') == 'lucasresck/bert-base-cased-ag-news') ? 'selected' : '' }}>BERT Base Engleză News</option>
            </select>
        </div>

        <div class="mb-3 form-check">
            <input
                type="checkbox"
                id="preprocess"
                name="preprocess"
                class="form-check-input"
                {{ (old('preprocess', $preprocess ?? 0) == 1) ? 'checked' : '' }}>
            <label for="preprocess" class="form-check-label">Titlul va fi preprocesat?</label>
        </div>

        <button type="submit" class="btn btn-primary">Analizează</button>
    </form>

    @if(isset($label) && isset($score))
        <div class="mt-4">
            <h3>Rezultatele Analizei:</h3>
            <p><strong>Etichetă (Label):</strong> {{ $label }}</p>
            <p><strong>Scor:</strong> {{ $score }}</p>
        </div>
    @endif
</div>
<script>
    const fetchTitleButton = document.getElementById('fetchTitle');
    const linkInput = document.getElementById('link');
    const titleInput = document.getElementById('title');

    fetchTitleButton.addEventListener('click', () => {
        debugger;

        const link = linkInput.value;

        if (!link) {
            alert('Introduceți un link valid.');
            return;
        }

        // Cerere AJAX către ruta Laravel
        $.ajax({
            url: '{{ route('fetchTitle') }}',
            type: 'POST',
            data: {
                link: link,
                _token: '{{ csrf_token() }}'
            },
            success: function (data) {
                if (data.title) {
                    titleInput.value = data.title; // Setăm titlul în câmpul de input
                } else if (data.error) {
                    alert(data.error); // Afișăm mesajul de eroare
                } else {
                    alert('Nu s-a putut obține titlul articolului.');
                }
            },
            error: function () {
                alert('A apărut o eroare la obținerea titlului.');
            }
        });
    });
</script>



<script>
    const modelDropdown = document.getElementById('model');

    window.addEventListener('load', () => {
        const savedModel = localStorage.getItem('selectedModel');

        if (savedModel) {
            modelDropdown.value = savedModel;
        }
    });

    modelDropdown.addEventListener('change', () => {
        const selectedModel = modelDropdown.value;
        localStorage.setItem('selectedModel', selectedModel);
    });
</script>
</body>
</html>
