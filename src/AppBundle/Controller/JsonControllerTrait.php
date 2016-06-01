<?php

namespace AppBundle\Controller;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Response;

trait JsonControllerTrait
{

	protected function jsonResponse(array $data, $code) {
		$defaultFields = [
			'status'    => null,
			'msg'       => null,
		];
		$data = array_merge($defaultFields, $data);
		$data = json_encode($data);
		return new Response($data, $code, array(
			'Content-Type' => 'application/json'
		));
	}
		
	/**
	 * Creates a JSON-response with a 200 status code
	 * 
	 * @param array $data
	 * @return Response
	 */
	protected function jsonSuccessResponse(array $data) {
		$data = $data + ['status' => true];
		if (!isset($data['msg'])) $data['msg'] = 'OK';
		return $this->jsonResponse($data, 200);
	}

	/**
	 * Creates a JSON-response with a 200 status code
	 *
	 * @param array $data
	 * @return Response
	 */
	protected function jsonFailureResponse(array $data, $message = null) {
		$data =
			$data + [
				'status'    => false,
				'msg'       => $message ?: 'Something went wrong',
			];
		return $this->jsonResponse($data, 500);
	}
}
