<?php namespace App\Http\Requests;

use App\Http\Requests\Request;

class CargoFuncionarioUpdateRequest extends Request {

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
            'fecha_inic' => 'required|date_format:Y-m-d',
            'fecha_culm' => 'date_format:Y-m-d',
            'fecha_ex'   => 'required|date_format:Y-m-d',
            'id_lcargo'  => 'required|min:1|max:4294967295|numeric',
            'id_area'    => 'required|min:1|max:4294967295|numeric',
            'id_desig'   => 'required|min:1|max:4294967295|numeric',
            'sta_contrato'  => 'required|max:20',
            'sta_cargo'     => 'required|max:20',            
            'observaciones' => 'max:190',            
        ];
    }

    public function messages()
    {
        return [
            'fecha_inic.required'   => 'Fecha de inicio requerido',
            'fecha_in.date_format'  => 'Fecha de inicio no tiene formato requerido',

            'fecha_culm.date_format'  => 'Fecha de culminación no tiene formato requerido',

            'fecha_ex.required'     => 'Fecha de expedición requerido',
            'fecha_ex.date_format'  => 'Fecha de expedición no tiene formato requerido',

            'id_lcargo.required'      => 'Cargo es requerido',
            'id_lcargo.min'           => 'Valor mínimo del cargo seleccionada no válido',
            'id_lcargo.max'           => 'Valor máximo del cargo seleccionada no válido',

            'id_area.required'      => 'Area es requerido',
            'id_area.min'           => 'Valor mínimo del área seleccionada no válido',
            'id_area.max'           => 'Valor máximo del área seleccionada no válido',

            'id_desig.required'      => 'Designación es requerido',
            'id_desig.min'           => 'Valor mínimo de la designación seleccionada no válido',
            'id_desig.max'           => 'Valor máximo de la designación seleccionada no válido',

            'sta_contrato.required'      => 'Estatus de contrato es requerido',
            'sta-contrato.max'           => 'Estatus de contrato varlo máximo del cargo seleccionado no válido',

            'sta_cargo.required'      => 'Estatus de contrato es requerido',
            'sta_cargo.max'           => 'Estatus de contrato varlo máximo del cargo seleccionado no válido',

            'observaciones.max'         => 'Observaciones tiene mas de 190 caractéres',
        ];
    }  

}
?>