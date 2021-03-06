<?php

namespace SomeNamespace;

use Rector\Symfony\Tests\Rector\FrameworkBundle\GetParameterToConstructorInjectionRector\Source\DoctrineQuery;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ClassWithNamedService3 extends Controller
{
    public function render()
    {
        $value = $this->getParameter('some_parameter');

        $query = new DoctrineQuery();
        $queryId = $query->getParameter('id');
    }
}

?>
-----
<?php

namespace SomeNamespace;

use Rector\Symfony\Tests\Rector\FrameworkBundle\GetParameterToConstructorInjectionRector\Source\DoctrineQuery;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ClassWithNamedService3 extends Controller
{
    /**
     * @var string
     */
    private $someParameter;
    public function __construct(string $someParameter)
    {
        $this->someParameter = $someParameter;
    }
    public function render()
    {
        $value = $this->someParameter;

        $query = new DoctrineQuery();
        $queryId = $query->getParameter('id');
    }
}

?>
