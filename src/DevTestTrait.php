<?php 

namespace devskyfly\robocmd;

trait DevTestTrait 
{

    /****************
     * User functions
     ***************/
    public function testsClear()
    {

    }

    /********************
     * End user functions
     *******************/

    /**
     * Run all test suites or one, optional it runs local server.
     * 
     * @param array $opts
     * @option string $suite "all"|"unit"|"functional"
     * @option boolean $localserver
     * @option boolean $debug
     */
    public function testsRun($opts = ["suite|s" => "all", "debug|d" => false,"localserver|l" => false])
    {
        $this->testsClear();
        
        $suites = [
            "all",
            "unit",
            "functional"
        ];

        if (!in_array($opts["suite"], $suites)) {
            throw new \InvalidArgumentException('Option suite is out of list:'.implode($suites));
        }

        if ($opts['localserver']) {
            $this->testsServerRun(["port" => 3000, "back" => true]);
        }

        $collection = $this->collectionBuilder();

        if ($opts['suite'] == "all" || $opts['suite'] == "unit") {
            $collection->addTask($this->taskExec(getcwd().'/vendor/bin/codecept run unit'.($opts['debug']?" --debug":"")));
        }

        if ($opts['suite'] == "all" || $opts['suite'] == "functional") {
            $collection->addTask($this->taskExec(getcwd().'/vendor/bin/codecept run functional'.($opts['debug']?" --debug":"")));
        }
        
        $collection->run();
    }

    protected function testsPath()
    {
        $path = getcwd()."/tests";
        if (!file_exists($path)) {
            throw new \RuntimeException("Dir {$path} does not exist.");
        }
        return $path;
    }

    protected function testsAppPath()
    {
        $path = $this->testsPath()."/app";
        if (!file_exists($path)) {
            throw new \RuntimeException("Dir {$path} does not exist.");
        }
        return $path;
    }

    protected function testsAppWebPath()
    {
        $path = $this->testsPath()."/app/web";
        if (!file_exists($path)) {
            throw new \RuntimeException("Dir {$path} does not exist.");
        }
        return $path;
    }

    /**
     * Run local server.
     *
     * --port integer
     */
    public function testsServerRun($opts = ["port|p" => "3000", "back|b" => false])
    {
        $dir = $this->testsAppWebPath();
        $server = $this->taskServer($opts["port"]);
        
        if ($opts["back"]) {
            $server->background();
        }

        $server->dir($dir)
        ->run();
    }

    public function testsMigrationsApply()
    {
        $appPath = $this->testsAppPath();
        $this->taskExec("{$appPath}/yii migrate/up")->run();
    }

    // Development

    /**
     * Init project.
     * 
     */
    public function devInitProject()
    {
        if ($this->confirm("Do you want to create database")) {
            $database = $this->ask("Input database name");
            $user = $this->ask("Input user name");
            $opts = ["database" => $database, "user" => $user];
            $this->devCreateDb($opts);
        }

        $this->devUpMigration();
        $this->testsMigrationsApply();
        $this->devAfterInitProject();
    }

    /**
     * Return project path.
     *
     * @return void
     */
    public function devProjectPath()
    {
        return getcwd();
    }

    /**
     * Your custom tasks after defult project init.
     *
     */
    public function devAfterInitProject()
    {
        //Your code
    }

    // Migrations

    /**
     * Create migration.
     *
     * @param array $opts
     * @return void
     */
    public function devCreateMigration($opts = ["migration|m" => ""])
    {
        if (empty($opts["migration"])) {
            throw new \InvalidArgumentException('Property $opts[\'migration\'] is empty.');
        }

        $app = $this->testsAppPath();
        $this->taskExec("{$app}/yii migrate/create {$opts["migration"]} --migrationPath=\"{$this->devProjectPath()}/src/migrations\"")->run();
    }

    /**
     * Apply migrations.
     *
     * @return void
     */
    public function devUpMigration()
    {
        $app = $this->testsAppPath();
        $this->taskExec("{$app}/yii migrate/up --migrationPath=\"{$this->devProjectPath()}/src/migrations\"")->run();
    }

    /**
     * Rollback migrations.
     *
     * @return void
     */
    public function devDownMigration()
    {
        $app = $this->testsAppPath();
        $this->taskExec("{$app}/yii migrate/down --migrationPath=\"{$this->devProjectPath()}/src/migrations\"")->run();
    }

    // Database

    /**
     * Create database.
     * 
     * @param array $opts
     * @option string $database
     * @option string $user
     */
    public function devCreateDb($opts = ["database|d" => "", "user" => "root"])
    {
        if (empty($opts['database'])) {
            throw new \InvalidArgumentException('Set database name.');
        }

        if (empty($opts['user'])) {
            throw new \InvalidArgumentException('Set user name.');
        }

        $this->taskExec("echo CREATE DATABASE IF NOT EXISTS {$opts['database']} CHARACTER SET utf8 COLLATE utf8_general_ci| mysql -u{$opts['user']} -p")->run();
    }

     /**
     * Drop database.
     * 
     * @param array $opts
     * @option string $database
     * @option string $user
     */
    public function devDropDb($opts = ["database|d" => "", "user" => "root"])
    {
        if (empty($opts['database'])) {
            throw new \InvalidArgumentException('Set database name.');
        }

        if (empty($opts['user'])) {
            throw new \InvalidArgumentException('Set user name.');
        }

        $this->taskExec("echo DROP DATABASE {$opts['database']} | mysql -u{$opts['user']} -p")->run();
    }
}