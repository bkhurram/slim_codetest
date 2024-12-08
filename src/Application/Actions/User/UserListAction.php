<?php

namespace App\Application\Actions\User;

use App\Application\Actions\Action;
use App\Application\Models\User;
use App\Application\Response\PaginateResponse;
use Psr\Http\Message\ResponseInterface as Response;

class UserListAction extends Action
{
	/**
	 * {@inheritdoc}
	 */
	protected function action(): Response
	{
		$params = $this->request->getQueryParams();
		$query = User::query()->with(['address']);

		if (isset($params['email'])) {
			foreach ($params['email'] as $email) {
				$query->orWhere('email', $email);
			}
		}

		$page = isset($params['page']) ? (int)$params['page'] : 1;
		$perPage = isset($params['perPage']) ? (int)$params['perPage'] : 10;
		$items = $query->paginate(perPage: $perPage, page: $page);

		return $this->respondWithData(new PaginateResponse($items));
	}
}
