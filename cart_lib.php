<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['cart'])) $_SESSION['cart'] = []; 
function cart_add($id, $qty=1){ $qty=max(1,(int)$qty); $_SESSION['cart'][$id] = ($_SESSION['cart'][$id] ?? 0) + $qty; }
function cart_set($id, $qty){ $_SESSION['cart'][$id] = max(1,(int)$qty); }
function cart_remove($id){ unset($_SESSION['cart'][$id]); }
function cart_clear(){ $_SESSION['cart'] = []; }
function cart_count(){ return array_sum($_SESSION['cart']); }
function cart_total($products){ $t=0; foreach($_SESSION['cart'] as $id=>$q){ if(isset($products[$id])) $t += $products[$id]['price']*$q; } return $t; }
