<?php namespace App\Http\Requests;

use App\Http\Requests\Request;

class LfuncionesUpdateRequest extends Request {

	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize()
	{
		return true;
	}

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
    public function rules()
    {
        return [
            'descripcion'        => 'required|max:190',
        ];
    }

    public function messages()
    {
        return [
            'descripcion.required'     => 'Descripcion de la función es requerido',
            'descripcion.max'          => 'Descripción de la función tiene máss de 190 caractéres',
        ];
    }  

}
?>