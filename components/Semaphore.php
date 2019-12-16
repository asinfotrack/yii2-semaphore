<?php
namespace asinfotrack\yii2\semaphore\components;

/**
 * Basic contract for semaphore implementations
 *
 * @author Pascal Mueller, AS infotrack AG
 * @link http://www.asinfotrack.ch
 * @license AS infotrack AG license / MIT, see provided license file
 */
interface Semaphore
{

	/**
	 * Acquire a semaphore
	 *
	 * @param string $id the id to identify the semaphore
	 * @param bool $wait whether or not to wait for the lock to become available
	 *
	 * @return bool true if successfully acquired and false if there was an error or the semaphore
	 * could not be acquired because it is locked already
	 */
	public function acquire(string $id, bool $wait = true) : bool;

	/**
	 * Release a semaphore
	 *
	 * @param string $id the id to identify the semaphore
	 *
	 * @return bool true if successfully released, false if the semaphore does not exist or if there
	 * was an error while releasing
	 */
	public function release(string $id) : bool;

}
