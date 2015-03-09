<?php
namespace Devture\Bundle\TranslationBundle\Model;

interface ResourceInterface {

	public function getName();

	public function getPath();

	public function getLocaleKey();

	public function isSource();

	public function setTranslationPack(TranslationPack $pack);

	/**
	 * @return TranslationPack
	 */
	public function getTranslationPack();

}