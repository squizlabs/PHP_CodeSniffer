<?php

$a = true;

// Warning
if ($a)
	echo true;

// Fixable errors
if ($a) {
	echo true;
}

if ($a)

{
	echo true;
}

// Note the echo here will not be indented by this sniff but by PEAR's MultiLineConditionSniff
if ($a)
{echo true;
}

if ($a)
     {
	echo true;
}

function muh()
{
	if ($a)
		{
		echo true;
	}

	if ($a)
{
		echo true;
	}

	// If statement with spaces which aren't equal to a tab space (4 spaces)
	if ($a)
	  {
		echo true;
	}
}

// NewlineAfterOpenBrace
if ($a)
{

	echo true;
}

while ($a < $b)
{


	echo 'muh';
}

// Closure indentation tests
function foo()
{
	// Sort the array by value of subarray
	usort(
		$languages,
		function($a, $b) use ($that)
		{
			$ordering = $that->getState('list.ordering');

			if (strtolower($that->getState('list.direction')) === 'asc')
		{
				return StringHelper::strcmp($a->$ordering, $b->$ordering);
			}
			else
		{
				return StringHelper::strcmp($b->$ordering, $a->$ordering);
			}
		}
	);
}

// Anonymous classes test
$this->attachRule(
	new class($this) implements RulesInterface
	{
		protected $router;

		public function __construct(RouterView $router)
		{
			$this->router = $router;
		}

		public function preprocess(&$query)
		{
		}

		public function parse(&$segments, &$vars)
		{
			// Get the views and the currently active query vars
			$views  = $this->router->getViews();
			$active = $this->router->menu->getActive();

			if ($active){
				$vars = array_merge($active->query, $vars);
			}

			// We don't have a view or its not a view of this component! We stop here
			if (!isset($vars['view']) || !isset($views[$vars['view']]))
{
				return;
			}

			// Get the item from the segment
			$segment = array_shift($segments);

			if (strpos($segment, '-') === false)
				{
				throw new InvalidArgumentException('Invalid URL', 404);
			}
			list ($view, $jcItemId) = explode('-', $segment);

			if (!isset($views[$view]))
		{
				throw new InvalidArgumentException('View not found', 404);
			}

			$db = Factory::getDbo();
			$vars['view'] = $view;

			// Move forward based on view
			switch ($view)
				{
				case 'tracker':
					// Search the database for the appropriate tracker.
					$db->setQuery(
						$db->getQuery(true)
							->select('tracker_id')
							->from('#__code_trackers')
							->where('jc_tracker_id = ' . (int) $jcItemId)
						,
						0,
						1
					);
					$trackerId = (int) $db->loadResult();
					$vars['tracker_id'] = $jcItemId;
					break;

				case 'issue';
					// Search the database for the appropriate issue.
					$db->setQuery(
						$db->getQuery(true)
							->select('issue_id')
							->from('#__code_tracker_issues')
							->where('jc_issue_id = ' . (int) $jcItemId)
						,
						0,
						1
					);
					$issueId = (int) $db->loadResult();
					$vars['issue_id'] = $jcItemId;
					break;
			}

			return $vars;
		}
	}
);
