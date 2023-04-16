<?php
// Demo extension, https://github.com/annaesvensson/yellow-demo

class YellowDemo {
    const VERSION = "0.8.9";
    public $yellow;         // access to API

    // Handle initialisation
    public function onLoad($yellow) {
        $this->yellow = $yellow;
    }
    
    // Handle page meta data
    public function onParseMetaData($page) {
        if ($page===$this->yellow->page) {
            $prefix = strtoloweru($this->yellow->language->getText("languageDescription", $page->get("language")));
            $page->set("editLoginEmail", "$prefix@demo.com");
            $page->set("editLoginPassword", "password");
        }
    }
    
    // Handle update
    public function onUpdate($action) {
        $fileNameUser = $this->yellow->system->get("coreExtensionDirectory").$this->yellow->system->get("coreUserFile");
        if ($action=="install") {
            foreach ($this->yellow->system->getAvailable("language") as $language) {
                $prefix = strtoloweru($this->yellow->language->getText("languageDescription", $language));
                $email = "$prefix@demo.com";
                if (!$this->yellow->user->isExisting($email)) {
                    $settings = array(
                        "name" => "Demo",
                        "description" => $this->yellow->language->getText("editUserDescription", $language),
                        "language" => $language,
                        "access" => "edit, upload",
                        "home" => "/demo/",
                        "hash" => $this->yellow->extension->get("edit")->response->createHash("password"),
                        "stamp" => $this->yellow->extension->get("edit")->response->createStamp(),
                        "pending" => "none",
                        "failed" => "0",
                        "modified" => date("Y-m-d H:i:s", time()),
                        "status" => "active");
                    $ok = $this->yellow->user->save($fileNameUser, $email, $settings);
                    $this->yellow->toolbox->log($ok ? "info" : "error", "Add user 'Demo'");
                }
            }
        } elseif ($action=="uninstall") {
            foreach ($this->yellow->system->getAvailable("email") as $email) {
                if (!preg_match("/@demo.com$/", $email)) continue;
                if ($this->yellow->user->getUser("home", $email)=="/") continue;
                $name = $this->yellow->user->getUser("name", $email);
                $ok = $this->yellow->user->remove($fileNameUser, $email);
                $this->yellow->toolbox->log($ok ? "info" : "error", "Remove user '".strtok($name, " ")."'");
            }
        }
    }
}
