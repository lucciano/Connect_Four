<?php
namespace FourInLine;

class Status {
	protected $data;
	protected $game_store;

        function __construct(){
		$this->game_store = '/tmp/game.json';
		$data = json_decode(@file_get_contents($this->game_store), true);
		if(!is_array($data['game'])){
			$this->data = $this->newGame();
		}else{
			$this->data = $data;
		}
	}

	function newGame(){
		$zero_col = array_fill (1, 7, 0);
		$game = array_fill(1,6, $zero_col);
		$data = array('players' => array(1,2), 'game' => $game, 'error' => array());
		$this->data = $data;
		return $data;
	}

	function __destruct(){
		file_put_contents($this->game_store, json_encode($this->data));
	}

	function move($user, $x, $y){
		if($user != $this->data['players'][0]){
			$this->data['error'][] = "Wait for your turn";
			return false;
		}
		if($this->data['game'][$x][$y] != 0){
			$this->data['error'][] = "That move was allready done";
			return false;
		}

		$this->data['players'] = array_merge(
				array_slice($this->data['players'],1), 
				array($this->data['players'][0]));
		while(isset($this->data['game'][$x+1][$y]) and $this->data['game'][$x+1][$y] == 0){
			$x += 1;
		}
		$this->data['game'][$x][$y] = $user;

		return true;
	}

	function export(){
		return $this->data;
	}
}
