<?php declare(strict_types=1);

namespace Siler\Example\GraphQL\Annotation;

use Siler\GraphQL\DateScalar;
use Siler\Route;
use function Siler\GraphQL\{annotated, debug, init};

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/vendor/autoload.php';

$schema = annotated([
    Upper::class,
    Foo::class,
    Bar::class,
    FooBar::class,
    TodoStatus::class,
    ITodo::class,
    Todo::class,
    TupleInput::class,
    Query::class,
    Mutation::class,
], [
    new DateScalar(),
]);

debug();

Route\post('/graphql', fn() => init($schema));
