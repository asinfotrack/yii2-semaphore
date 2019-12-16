<?php
namespace asinfotrack\yii2\semaphore\components;

use Yii;
use yii\base\InvalidConfigException;
use yii\helpers\FileHelper;

/**
 * File based semaphore component for the yii2-framework
 *
 * @author Pascal Mueller, AS infotrack AG
 * @link http://www.asinfotrack.ch
 * @license AS infotrack AG license / MIT, see provided license file
 */
class FileSemaphore extends \yii\base\Component implements \asinfotrack\yii2\semaphore\components\Semaphore
{

	/**
	 * @var string holds the absolute path to the lock folder without a
	 * trailing slash
	 */
	protected $lockFolderPath;

	/**
	 * @var resource[] holds the pointers to the lock files and is indexed by their ids
	 */
	protected $lockFilePointers = [];

	/**
	 * @var string the alias to the folder holding the lock files
	 */
	public $lockFolderAlias;

	/**
	 * {@inheritDoc}
	 *
	 * @throws \yii\base\InvalidConfigException if the lock folder alias is not defined in the config file
	 * @throws \yii\base\Exception if the folder holding the semaphore files could not be created
	 */
	public function init()
	{
		parent::init();

		if ($this->lockFolderAlias === null) {
			$msg = Yii::t('el/validation', 'The lock folder alias must be set');
			throw new InvalidConfigException($msg);
		}

		$this->lockFolderPath = FileHelper::normalizePath(Yii::getAlias($this->lockFolderAlias));
		if (!file_exists($this->lockFolderPath) || !is_dir($this->lockFolderPath)) {
			FileHelper::createDirectory($this->lockFolderPath);
		}
	}

	/**
	 * {@inheritDoc}
	 */
	public function acquire(string $id, bool $wait = true) : bool
	{
		$path = $this->createLockFilePath($id);

		if (!file_exists($path) || is_dir($path)) {
			if (!touch($path)) {
				return false;
			}
		}

		$flockOptions = $wait ? LOCK_EX : LOCK_EX|LOCK_NB;
		$fp = fopen($path, 'r+');
		if ($fp === false || !flock($fp, $flockOptions)) {
			return false;
		}

		$this->lockFilePointers[$id] = $fp;
		return true;
	}

	/**
	 * {@inheritDoc}
	 */
	public function release(string $id) : bool
	{
		if (!isset($this->lockFilePointers[$id])) {
			return false;
		}

		return fclose($this->lockFilePointers[$id]);
	}

	/**
	 * Generates the absolute path to a semaphore file
	 *
	 * @param string $id the id of the semaphore
	 *
	 * @return string the absolute path to the semaphore
	 */
	protected function createLockFilePath(string $id) : string
	{
		return $this->lockFolderPath . DIRECTORY_SEPARATOR . md5($id);
	}

}
