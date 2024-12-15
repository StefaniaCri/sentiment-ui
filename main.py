from flask import Flask, request, jsonify
from transformers import pipeline
import validators
from newspaper import Article

app = Flask(__name__)

@app.route('/get-title', methods=['POST'])
def get_title_or_string():
    data = request.json
    input_str = data.get('url')
    print(input_str)
    if validators.url(input_str):
        try:
            article = Article(input_str)
            article.download()
            article.parse()
            return jsonify({'title': article.title}) if article.title else "Title not found"
        except Exception as e:
            return jsonify({'error':  f"Error retrieving title: {str(e)}"})
    else:
        return jsonify({'title': input_str})

@app.route('/predict', methods=['POST'])
def predict():
    data = request.json
    text = data['text']

    model = data['model']
    preprocess = data['preprocess']
    if model == "bert-base-spanish-wwm-uncased":
        pipe = pipeline("text-classification", model="StefaniaCri/bert-spanish-sentiment")
    elif model == "M47Labs/spanish_news_classification_headlines" and preprocess:
        pipe = pipeline("text-classification", model="StefaniaCri/bert-spanish-sentiment_M47labs_preproces")
    elif model == "M47Labs/spanish_news_classification_headlines" and not preprocess:
        pipe = pipeline("text-classification", model="StefaniaCri/bert-spanish-sentiment_M47labs")
    elif model == "dumitrescustefan/bert-base-romanian-cased-v1" and preprocess:
        pipe = pipeline("text-classification", model="StefaniaCri/bert-romanian-news-with_prep")
    elif model == "dumitrescustefan/bert-base-romanian-cased-v1" and not preprocess:
        pipe = pipeline("text-classification", model="StefaniaCri/bert-romanian-news-without_prep")
    elif model == "lucasresck/bert-base-cased-ag-news" and preprocess:
        pipe = pipeline("text-classification", model="StefaniaCri/bert-english-sentiment-news_preprocessed")
    elif model == "lucasresck/bert-base-cased-ag-news" and not preprocess:
        pipe = pipeline("text-classification", model="StefaniaCri/bert-english-sentiment_without_prep")
    else:
        pipe = pipeline("text-classification", model="StefaniaCri/bert-spanish-sentiment")

    result = pipe(text)

    label_mapping = {
        "LABEL_1": "NEUTRU",
        "LABEL_0": "NEGATIV",
        "LABEL_2": "POZITIV"
    }
    for item in result:
        item["label"] = label_mapping.get(item["label"], item["label"])

    return jsonify(result)

if __name__ == "__main__":
    app.run(host='0.0.0.0', port=5000)
