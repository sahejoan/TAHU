<?php namespace App\Http\Requests;

use App\Http\Requests\Request;

class PermisosCreateRequest extends Request {

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
            'name'        => 'required|max:190',
            'guard_name'   => 'required|max:190',
        ];
    }

    public function messages()
    {
        return [
            'name.required'     => 'Nonbre del permiso es requerido',
            'name.max'          => 'Nombre del permiso tiene máss de 190 caractéres',

            'guard_name.required'   => 'Guardian es requerido',
            'guard_name.max'        => 'Guardian tiene más de 190 caractéres',
        ];
    }  

}
?>