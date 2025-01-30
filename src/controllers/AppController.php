<?php

class AppController {
    private $request;

    public function __construct()
    {
        session_start();
        
        // Allow these actions without a session
        $allowedRoutes = ['login', 'signup', 'changepassword'];
        $adminRoutes = ['adminpage', 'logout', 'removeUser'];
        
        $currentRoute = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $currentRoute = trim($currentRoute, '/'); 
        
        // Log the current route for debugging
        error_log("Current Route: " . $currentRoute);
        
        if (!isset($_SESSION['user_id'])) {
            if(!in_array($currentRoute, $allowedRoutes)) {
                header('Location: /login');
                exit();
            }
        }
        else {
            if($_SESSION['userType'] == 'admin') {
                if(!in_array($currentRoute, $adminRoutes))
                {
                    header('Location: /adminpage');
                    exit();
                }
            }
            else{
                if (in_array($currentRoute, $allowedRoutes) || $currentRoute == 'adminpage') {  
                    header('Location: /feed');
                    exit();
                }

            }
        }
        $this->request = $_SERVER['REQUEST_METHOD'];
    }


    protected function isGet(): bool
    {
        return $this->request === 'GET';
    }

    protected function isPost(): bool
    {
        return $this->request === 'POST';
    }

    protected function render(string $template = null, array $variables = [])
    {
        $templatePath = 'public/views/'. $template.'.php';
        $output = 'File not found';
                
        if(file_exists($templatePath)){
            extract($variables);
            
            ob_start();
            include $templatePath;
            $output = ob_get_clean();
        }
        print $output;
    }
}