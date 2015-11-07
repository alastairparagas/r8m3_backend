<?php

class BaseController extends Controller {

    /**
     * Setup the layout used by the controller.
     *
     * @return void
     */
    protected function setupLayout() {
        if (!is_null($this->layout)) {
            $this->layout = View::make($this->layout);
        }
    }

    protected function jsonResponse($status, $message, $callback='', $info='') {
        $json_info = array();
        $status_code = "";
        $json_info['message'] = $message;
        $json_info['info'] = empty($info) ? '' : $info->toArray();
        
        if ($status == "ok") {
            $json_info['status'] = 'ok';
            $status_code = 200;
        } else {
            $json_info['status'] = 'error';
            $status_code = 400;
        }
        
        if(!empty($callback)){
            return Response::json($json_info, $status_code)->setCallback($callback);
        }
        
        return Response::json($json_info, $status_code);
    }

}
