<?php

namespace Jdlx\Generator;

use Jdlx\Generator\Path\File;
use Jdlx\Generator\Path\StubFile;
use Jdlx\Generator\Printer\AttributesDocBlock;
use Jdlx\Generator\Printer\FactoryFields;
use Jdlx\Generator\Printer\FieldAccess;
use Jdlx\Generator\Printer\FieldCast;
use Jdlx\Generator\Printer\FieldFillable;
use Jdlx\Generator\Printer\FieldHidden;
use Jdlx\Generator\Printer\JSModelFields;
use Jdlx\Generator\Printer\ResourceFields;
use Jdlx\Generator\Stub\JsStub;
use Jdlx\Generator\Stub\PhpStub;

class ModelGenerator
{

    public const PAGE_TYPES = ["Add", "Edit", "List", "View"];
    public const COMPONENT_TYPES = ["AddCard", "Details", "EditCard", "Form", "List", "ViewCard"];

    use ModelUtil;

    /**
     * Name of the resulting model
     *
     * @var Model
     */
    protected $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * (re)generate the files that should
     * not be touched by the developer
     *
     * @return File
     */
    public function generate(): bool
    {
        $this->generateModelFields();
        $this->generateModelResource();
        $this->generateModelCrudFieldsTrait();
        $this->generateModelCrudRoutesTrait();
        $this->generateModelPolicyTraits();

        if ($this->model->exists()) {
            $this->generateFrontEnd();
        }

        return true;
    }


    /**
     * (re)generate scaffolding files
     *
     * @param false $overwrite
     * @return bool
     * @throws Exception\FileExistsException
     */
    public function scaffold(bool $overwrite = false, bool $ignoreExists = false): bool
    {
        $this->generateModelScaffold($overwrite, $ignoreExists);
        $this->generateModelControllerScaffold($overwrite, $ignoreExists);
        $this->generateModelResourceScaffold($overwrite, $ignoreExists);
        $this->generateModelFactoryScaffold($overwrite, $ignoreExists);
        $this->generatePolicyScaffold($overwrite, $ignoreExists);

        $ns = $this->model->getModelName();
        foreach (self::PAGE_TYPES as $type) {
            $this->generateFrontEndPage($type, $ns, "dashboard/", $overwrite, $ignoreExists);
        }
        foreach (self::COMPONENT_TYPES as $type) {
            $this->generateFrontEndComponent($type, $ns, null, $overwrite, $ignoreExists);
        }

        $this->generateFrontEndRouter($ns, $overwrite, $ignoreExists);

        return true;
    }

    /**
     * @throws Exception\FileExistsException
     */
    public function generateModelScaffold($overwrite, $ignoreExists = false)
    {
        $path = $this->model->getModelScaffoldPath();

        // Prep the STUB
        $stub = new PhpStub(StubFile::phpStub("ScaffoldModel"));
        $this->model->addStubReplacements($stub);

        $path->writeContents($stub->generate(), $overwrite, $ignoreExists);
    }

    /**
     * @throws Exception\FileExistsException
     */
    public function generateModelControllerScaffold($overwrite, $ignoreExists = false)
    {
        $path = $this->model->getControllerScaffoldPath();

        // Prep the STUB
        $stub = new PhpStub(StubFile::phpStub("ScaffoldModelController"));
        $this->model->addStubReplacements($stub);

        $path->writeContents($stub->generate(), $overwrite, $ignoreExists);
    }

    /**
     * @throws Exception\FileExistsException
     */
    public function generateModelFactoryScaffold($overwrite, $ignoreExists = false)
    {
        $path = $this->model->getFactoryScaffoldPath();

        // Prep the STUB
        $stub = new PhpStub(StubFile::phpStub("ScaffoldModelFactory"));
        $this->model->addStubReplacements($stub);

        $path->writeContents($stub->generate(), $overwrite, $ignoreExists);
    }

    /**
     * @throws Exception\FileExistsException
     */
    public function generateModelResourceScaffold($overwrite, $ignoreExists = false)
    {
        $path = $this->model->getResourceScaffoldPath();

        // Prep the STUB
        $stub = new PhpStub(StubFile::phpStub("ScaffoldModelResource"));
        $this->model->addStubReplacements($stub);

        $path->writeContents($stub->generate(), $overwrite, $ignoreExists);
    }

    /**
     * @throws Exception\FileExistsException
     */
    public function generatePolicyScaffold($overwrite, $ignoreExists = false)
    {
        $path = $this->model->getPolicyScaffoldPath();

        // Prep the STUB
        $stub = new PhpStub(StubFile::phpStub("ModelPolicy"));
        $this->model->addStubReplacements($stub);

        $path->writeContents($stub->generate(), $overwrite, $ignoreExists);
    }


    /**
     * @throws Exception\FileExistsException
     */
    public function createModelPolicyScaffold($overwrite, $ignoreExists = false)
    {
        $path = $this->model->getResourceScaffoldPath();

        // Prep the STUB
        $stub = new PhpStub(StubFile::phpStub("ScaffoldModelResource"));
        $this->model->addStubReplacements($stub);

        $path->writeContents($stub->generate(), $overwrite, $ignoreExists);
    }

    /**
     * @throws Exception\FileExistsException
     */
    public function generateModelCrudFieldsTrait()
    {
        $path = $this->model->getCrudFieldsTraitPath();
        $fieldInfo = $this->model->getFieldInfo();

        // Prep the STUB
        $stub = new PhpStub(StubFile::phpStub("WithModelCrudFields"));
        $this->model->addStubReplacements($stub);
        (new AttributesDocBlock($fieldInfo))->addToStub($stub);

        $path->writeContents($stub->generate(), true);
    }

    /**
     * @throws Exception\FileExistsException
     */
    public function generateModelCrudRoutesTrait()
    {
        $path = $this->model->getCrudRoutesTraitPath();

        // Prep the STUB
        $stub = new PhpStub(StubFile::phpStub("WithModelCrudRoutes"));
        $this->model->addStubReplacements($stub);

        $path->writeContents($stub->generate(), true);
    }

    /**
     * @throws Exception\FileExistsException
     */
    public function generateModelFields()
    {
        $path = $this->model->getFieldsPath();
        $fieldInfo = $this->model->getFieldInfo();
        $fieldAccess = $this->model->getFieldAccess();

        // Prep the STUB
        $stub = new PhpStub(StubFile::phpStub("ModelFields"));
        $this->model->addStubReplacements($stub);

        (new FieldAccess($fieldAccess))->addToStub($stub);
        (new FieldCast($fieldInfo))->addToStub($stub);
        (new FieldFillable($fieldAccess))->addToStub($stub);
        (new FieldHidden($fieldAccess))->addToStub($stub);
        (new FactoryFields($fieldInfo))->addToStub($stub);
        (new ResourceFields($fieldInfo, $fieldAccess))->addToStub($stub);

        $path->writeContents($stub->generate(), true);
    }

    /**
     * @throws Exception\FileExistsException
     */
    public function generateModelResource()
    {
        $path = $this->model->getResourcePath();

        // Prep the STUB
        $stub = new PhpStub(StubFile::phpStub("ModelResource"));
        $this->model->addStubReplacements($stub);

        $path->writeContents($stub->generate(), true);
    }

    public function generateModelPolicyTraits()
    {
        $path = $this->model->getGlobalPolicyPath();

        // Prep the STUB
        $stub = new PhpStub(StubFile::phpStub("UseModelPolicyGlobal"));
        $this->model->addStubReplacements($stub);

        $path->writeContents($stub->generate(), true);

        $path = $this->model->getOwnedPolicyPath();
        // Prep the STUB
        $stub = new PhpStub(StubFile::phpStub("UseModelPolicyOwned"));
        $this->model->addStubReplacements($stub);

        $path->writeContents($stub->generate(), true);
    }

    public function generateFrontEnd()
    {
        $dir = $this->model->getFrontEndStubDirectory();

        $paths = $dir->getStubs();
        $destPath = $this->model->getFrontEndDestinationDirectory();

        $fieldInfo = $this->model->getFieldDefinitions(true, true);

        // Prep the STUB
        foreach ($paths as $path) {

            $stub = new JsStub(new StubFile($path['path']));
            $this->model->addStubReplacements($stub);
            (new JSModelFields($fieldInfo))->addToStub($stub);
            $file = new File($destPath . $stub->generatePath($path['dest']));
            $file->writeContents($stub->generate(), true);

        }
    }

    public function generateFrontEndComponent($type, $namespace, $path = "", $force = false, $ignoreExists = false)
    {
        $source = __DIR__ . "/stubs/js/scaffold/components/__MODEL__${type}.stub.js";
        $dest = empty($path) ? "" : trim($path, "/") . "/";

        $dest = "/src/components/{$dest}__LC_MODEL__/__NAMESPACE__${type}.js";

        return $this->generateFrontEndScaffoldFile($source, $dest, $namespace, $force, $ignoreExists);
    }

    public function generateFrontEndPage($type, $namespace, $path = "", $force = false, $ignoreExists = false)
    {
        $source = __DIR__ . "/stubs/js/scaffold/pages/__MODEL__${type}Page.stub.js";
        $dest = empty($path) ? "" : trim($path, "/") . "/";

        $dest = "/src/pages/{$dest}__LC_MODEL__/__NAMESPACE__${type}Page.js";
        return $this->generateFrontEndScaffoldFile($source, $dest, $namespace, $force, $ignoreExists);
    }

    public function generateFrontEndRouter($namespace, $force = false, $ignoreExists = false)
    {
        $source = __DIR__ . "/stubs/js/scaffold/router/__MODEL__CrudRoutes.stub.js";

        $dest = "/src/routers/__NAMESPACE__CrudRoutes.js";

        return $this->generateFrontEndScaffoldFile($source, $dest, $namespace, $force, $ignoreExists);
    }

    public function generateFrontEndScaffoldFile($source, $destination, $namespace, $force = false, $ignoreExists = false)
    {

        $stub = new JsStub(new StubFile($source));
        $this->model->addStubReplacements($stub);
        $stub->generatePlaceholders("namespace", $namespace);

        $destPath = Path::getFullPath("/webclient") . $stub->generatePath($destination);

        $file = new File($destPath);
        $file->writeContents($stub->generateForDestination($destPath), $force, $ignoreExists);

        return realpath($destPath);
    }

}
