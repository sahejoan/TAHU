<?php namespace App\Http\Requests;

use App\Http\Requests\Request;

class JefeDependenciaCreateRequest extends Request {

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
            'nom_jefe'        => 'required|max:190',
			'cedula'    => 'required|regex:/^[VE]-[0-9]{5,8}$/|max:10',            
        ];
    }

    public function messages()
    {
        return [
            'nom_jefe.required'     => 'Nombre es requerido',
            'nom_jefe.max'          => 'Nombre tiene máss de 190 caractéres',

            'cedula.required'      => 'Cédula es requerido',
            'regex'                => 'Formato de cédula invalido',
            'cedula.max'           => 'Cédula tiene más de 10 caractéres',
        ];
    }  

}
?>