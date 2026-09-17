<?php namespace App\Http\Requests;

use App\Http\Requests\Request;

class FuncionarioUpdateRequest extends Request {

    var $id;

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
            'cedula'    => 'required|regex:/^[VE]-[0-9]{5,8}$/|max:10|unique:funcionarios,cedula,'.$this->id,
            'rif'       => 'required|regex:/^[VEJPG]-[0-9]{5,8}-[0-9A-Z]{1}$/|max:12|unique:funcionarios,rif,'.$this->id,
            'apellido'  => 'required|max:190',
            'nombre'    => 'required|max:190',
            'direccion' => 'required|max:190',
            'fecha_nac' => 'required|date_format:Y-m-d',
            'sexo'      => 'required|max:1',
            'imagen'    => 'image|mimes:jpg,png,svg|max:1024',
            'telefono_p'   => 'max:15',
            'telefono_cont'   => 'max:15',
            'fecha_in'     => 'required|date_format:Y-m-d',
            'condicion_in' => 'required|max:30',
            'correo_alt'   => 'email|max:190',
            'correo_i'    => 'email|max:190',
            'tipo_s'      => 'max:30',
            'alergia'     => 'max:190',            
        ];
    }

    public function messages()
    {
        return [
            'cedula.required'      => 'Cédula es requerido',
            'regex'                => 'Formato de cédula invalido',
            'cedula.max'           => 'Cédula tiene más de 10 caractéres',
            'cedula.unique'        => 'Cédula ya existe',

            'rif.required'          => 'Rif es requerido',
            'regex'                 => 'Formato de RIF. invalido',
            'rif.max'               => 'Rif tiene más de 11 caractéres',
            'rif.unique'            => 'Rif ya existe',

            'apellido.required'     => 'Apellido es requerido',
            'cedula.max'            => 'Apellido tiene más de 190 caractéres',

            'nombre.required'       => 'Nombre es requerido',
            'nombre.max'            => 'Nombre tiene más de 190 caractéres',

            'direccion.required'    => 'Direción es requerido',
            'direccion.max'         => 'Dirección tiene más de 190 caractéres',

            'fecha_nac.required'     => 'Fecha de nacimiento requerido',
            'fecha_nac.date_format'  => 'Fecha de nacimiento no tiene formato requerido',

            'sexo.required'         => 'Sexo es requerido',
            'sexo.max'              => 'Sexo tiene más de 1 caractér',

            'telefono_p.max'          => 'Teléfono personal tiene más de 15 caractér',
            'telefono_cont.max'       => 'Teléfono contacto tiene más de 15 caractér',

            'fecha_in.required'     => 'Fecha de ingreso requerido',
            'fecha_in.date_format'  => 'Fecha de ingreso no tiene formato requerido',

            'condicion_in.required' => 'Condición requerido',
            'condicion_in.max'      => 'Condición tiene mas de 30 caractéres',

            'correo_alt.email'      => 'Formato de correo personal no es valido',
            'correo_alt.max'        => 'Correo personal tiene mas de 190 caractéres',

            'correo_i.email'      => 'Formato de correo institucional no es valido',
            'correo_i.max'        => 'Correo institucional tiene mas de 190 caractéres',            

            'tipo_s.max'          => 'El tipo de sangre tiene mas de 30 caractéres',
            'alergia.max'         => 'La descripción de alergia tiene mas de 190 caractéres'            
        ];
    }  

    public function setId($id) {
        $this->id=$id;
    }
}
?>