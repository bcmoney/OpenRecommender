  public function ShellSort()
  {
        $increment = 3;                 

        while ($increment > 0)
        {
              for ($i = 0; $i < $this->data->count(); $i++)
              {
                    $tmp = $this->data[$i];
                    $j = $i;                       

                    while ($j >= $increment)
                    {
                          if ($this->data[$j - $increment])
                          {
                                if (strcmp($this->data[$j -
$increment]->GetSortKey(), 
                                      $tmp->GetSortKey()) > 0)
                                {
                                      $this->data->offsetSet($j, 
                                            $this->data[$j -
$increment]);
                                      $j -= $increment;
                                }
                          }
                    }
                    $this->data->offsetSet($j, $tmp);
              }
               
              if ($increment % 2 != 0)
                    $increment = ($increment - 1) / 2;
              elseif ($increment == 1)
                    $increment = 0;
              else 
                    $increment = 1;
        }
 }