<?php
namespace LMQ;
interface Handler{
	public function __construct(array $config);
	public function push($message,$topic);
	public function pop($topic);
}