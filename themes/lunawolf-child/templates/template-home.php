<?php
/**
 * Template Name: Home
 * Description: A Home page template
 */

$context     = Timber::context();
$timber_post = Timber::get_post();
$context['post'] = $timber_post;
$context['content'] = get_flexible('flexible');

$templates = ['templates/template-home.twig', 'page.twig'];
Timber::render( $templates, $context );