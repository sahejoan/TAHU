<?php namespace App\Http\Requests;

use App\Http\Requests\Request;

class DesignacionUpdateRequest extends Request {
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
            'numdesig'    => 'max:10',
            'fecha_desig' => 'date_format:Y-m-d',
            'fecha_cese' =>  'date_format:Y-m-d',
            'estatus'     => 'required|max:10',
            'id_func'     => 'required|min:1|max:4294967295|numeric',
            'id_dep'      => 'required|min:1|max:4294967295|numeric',
            'id_jefedep'  => 'required|min:1|max:4294967295|numeric',
            'id_area'     => 'required|min:1|max:4294967295|numeric',            
            'id_divi'     => 'required|min:1|max:4294967295|numeric',            
            'id_jefediv'  => 'required|min:1|max:4294967295|numeric',            
            'id_jefeth'  => 'required|min:1|max:4294967295|numeric',                        
            'id_firma'     => 'required|min:1|max:4294967295|numeric',            
        ];
    }

    public function messages()
    {
        return [
            'numdesig.required'    => 'Número de la designación es requerido',
            'numdesig.unique'      => 'Número de la designación está registrado',
            'numdesig.max'         => 'Número de la designación tiene más de 10 caractéres',

            'fecha_desig.required'   => 'Fecha de la designación es requerido',
            'fecha_desig.date_format'=> 'Fecha de la designación no tiene formato requerido',

            'fecha_cese.date_format'=> 'Fecha del cese no tiene formato requerido',

            'estatus.required'      => 'Estatus es requerido',
            'estatus.max'           => 'Varlo máximo del estatus seleccionado no válido',

            'id_func.required'      => 'Funcionario es requerido',
            'id_func.min'           => 'Valor mínimo del funcionario seleccionado no válido',
            'id_func.max'           => 'Valor máximo del funcionario seleccionado no válido',

            'id_dep.required'      => 'Dependencia es requerido',
            'id_dep.min'           => 'Valor mínimo de la dependencia seleccionada no válido',
            'id_dep.max'           => 'Valor máximo de la dependencia seleccionada no válido',

            'id_jefedep.required'      => 'Jéfe de dependencia es requerido',
            'id_jefedep.min'           => 'Valor mínimo del jéfe de dependencia seleccionada no válido',
            'id_jefedep.max'           => 'Valor máximo del jefe de dependencia seleccionada no válido',

            'id_area.required'      => 'Area es requerido',
            'id_area.min'           => 'Valor mínimo del área seleccionada no válido',
            'id_area.max'           => 'Valor máximo del área seleccionada no válido',            

            'id_divi.required'      => 'División es requerido',
            'id_divi.min'           => 'Valor mínimo de la divisón seleccionada no válido',
            'id_divi.max'           => 'Valor máximo de la divisón seleccionada no válido',            

            'id_jefediv.required'      => 'Jefe de división es requerido',
            'id_jefediv.min'           => 'Valor mínimo del jéfe de divisón seleccionada no válido',
            'id_jefediv.max'           => 'Valor máximo deñ jéfe de divisón seleccionada no válido',            

            'id_jefeth.required'      => 'Jefe de talento humano es requerido',
            'id_jefeth.min'           => 'Valor mínimo del jefe de talento humano seleccionada no válido',
            'id_jefeth.max'           => 'Valor máximo del jefe de talento humano seleccionada no válido',            

            'id_firma.required'      => 'Funcionario que firma es requerido',
            'id_firma.min'           => 'Valor mínimo del funcionario que firma seleccionada no válido',
            'id_firma.max'           => 'Valor máximo del funcionario que firma seleccionada no válido',            
        ];
    }

    public function setId($id) {
        $this->id=$id;
    }      
}
?>