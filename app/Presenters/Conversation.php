<?php
/**
 * Forum presenter class.
 *
 * @author    MyBB Group
 * @version   2.0.0
 * @package   mybb/core
 * @license   http://www.mybb.com/licenses/bsd3 BSD-3
 */

namespace MyBB\Core\Presenters;

use Illuminate\Contracts\Auth\Guard;
use Illuminate\Database\DatabaseManager;
use McCool\LaravelAutoPresenter\BasePresenter;
use MyBB\Core\Database\Models\Conversation as ConversationModel;

class Conversation extends BasePresenter
{
	/** @var ConversationModel $wrappedObject */

	/**
	 * @var Guard
	 */
	private $guard;

	/**
	 * @param ConversationModel $resource
	 * @param Guard             $guard
	 */
	public function __construct(ConversationModel $resource, Guard $guard)
	{
		$this->wrappedObject = $resource;
		$this->guard = $guard;
	}

	/**
	 * @return ConversationMessage
	 */
	public function lastMessage()
	{
		if ($this->wrappedObject->lastMessage instanceof ConversationMessage) {
			return $this->wrappedObject->lastMessage;
		}

		return app()->make('MyBB\Core\Presenters\ConversationMessage', [$this->wrappedObject->lastMessage]);
	}

	/**
	 * @param User $user
	 *
	 * @return bool
	 */
	public function isUnread(User $user = null)
	{
		if ($user == null) {
			$user = $this->guard->user();
		}

		$participantData = $this->wrappedObject->participants->find($user->id)->pivot;

		if ($participantData->last_read == null) {
			return true;
		}

		if ($participantData->last_read < $this->wrappedObject->lastMessage->created_at) {
			return true;
		}

		return false;
	}
}
