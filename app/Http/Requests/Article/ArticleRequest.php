<?php

namespace App\Http\Requests\Article;

use App\Models\Article;
use Illuminate\Foundation\Http\FormRequest;

class ArticleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            Article::TITLE => 'required|String|min:1',
            Article::DESCRIPTION => 'required|String|min:1',
            Article::CATEGORY_KEY => 'required|String|exists:article_categories,key',
            Article::PRICE => 'required|Numeric|min:0.01',
            Article::VAT_PERCENT => 'required|Numeric|min:0.01|max:100',
        ];
    }
}
