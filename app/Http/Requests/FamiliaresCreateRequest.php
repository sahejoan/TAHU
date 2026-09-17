<?php namespace App\Http\Requests;

use App\Http\Requests\Request;

class FamiliaresCreateRequest extends Request {

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
            'cedula'    => 'required|regex:/^[VE]-[0-9]{5,8}$/|unique:familiares,cedula|max:10',
            'apellido'  => 'required|max:190',
            'nombre'    => 'required|max:190',
            'fecha_nac' => 'required|date_format:Y-m-d',
            'parentesco' => 'required|max:15',
        ];
    }

    public function messages()
    {
        return [
            'cedula.required'      => 'Cédula es requerido',
            'regex'                => 'Formato de cédula invalido',
            'cedula.max'           => 'Cédula tiene más de 10 caractéres',
            'cedula.unique'        => 'Cédula ya existe',

            'apellido.required'     => 'Apellido es requerido',
            'apellido.max'            => 'Apellido tiene más de 190 caractéres',

            'nombre.required'       => 'Nombre es requerido',
            'nombre.max'            => 'Nombre tiene más de 190 caractéres',

            'fecha_nac.required'     => 'Fecha de nacimiento requerido',
            'fecha_nac.date_format'  => 'Fecha de nacimiento no tiene formato requerido',

            'parentesco.required'         => 'Parentesco es requerido',
            'parentesco.max'              => 'Parentesco tiene más de 15 caractér',            
        ];
    }  

}
?>