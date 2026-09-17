<?php namespace App\Http\Requests;

use App\Http\Requests\Request;

class DivisionCreateRequest extends Request {

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
        	'id_jefedep'		 => 'required|min:1|max:4294967295|numeric',
            'descripcion'        => 'required|max:190',
        ];
    }

    public function messages()
    {
        return [
            'id_jefedep.required'      => 'Jéfe divisón es requerido',
            'id_jefedep.min'           => 'Valor mínimo del jéfe de divisón seleccionado no válido',
            'id_jefedep.max'           => 'Valor máximo del jéfe de divisón seleccionado no válido',

            'descripcion.required'     => 'Descripción de la división es requerido',
            'descripcion.max'          => 'Descripción de la división tiene máss de 190 caractéres',
        ];
    }  

}
?>