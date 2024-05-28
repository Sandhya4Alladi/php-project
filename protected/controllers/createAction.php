<?php
    class createAction extends  CAction{
        public function run(){
            $model = new User;
			
		if (isset($_POST['User'])) {
			$model->attributes = $_POST['User'];
			$saved = $model->save();
			// echo "<pre>";print_r($model);exit;

			if ($saved) {

				$this->redirect(array('view', 'id' => $model->id));
			}
		}
		$this->render('create', array(
			'model' => $model,
			// 'sandip'=>["test"=>"test"],
		));
    }
    }
?>