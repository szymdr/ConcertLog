<?php

class AppController {
    private $request;

    public function __construct()
    {
        session_start();
        
        // Allow these actions without a session
        $allowedRoutes = ['login', 'signup', 'changepassword'];
        
        $currentRoute = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $currentRoute = trim($currentRoute, '/'); 
        
        if (!isset($_SESSION['user_id']) && !in_array($currentRoute, $allowedRoutes)) {
            header('Location: /login');
            exit();
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