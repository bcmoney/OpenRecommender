public function MergeSort()
  {
        $this->MSort($this->data, array(), 0, $this->data->count
() - 1);
  }     

  private function MSort($data, $temp, $left, $right)
  {
        if ($right > $left)
        {
              $mid = ($right + $left) / 2;
              $this->MSort($data, $temp, $left, $mid);
              $this->MSort($data, $temp, $mid + 1,
$right);                 

              $this->Merge($data, $temp, $left, $mid + 1,
$right);
        }
  }
  private function Merge($data, $temp, $left, $mid, $right)
  {
        $leftEnd = $mid - 1;
        $tmpPos = $left;
        $numElements = $right - $left + 1;           

        while (($left <= $leftEnd) && ($mid <= $right))
        {
              if (strcmp($data[$left]->GetSortKey(),
                    $data[$mid]->GetSortKey()) <= 0)
              {
                    $temp[$tmpPos] = $data[$left];
                    $tmpPos++;
                    $left++;
              }
              else
              {
                    $temp[$tmpPos] = $data[$mid];
                    $tmpPos++;
                    $mid++;
              }
        }           

        while ($left <= $leftEnd)
        {
              $temp[$tmpPos] = $data[$left];
              $left++;
              $tmpPos++;
        }
        while ($mid <= $right)
        {
              $tmp[$tmpPos] = $data[$mid];
              $mid++;
              $tmpPos++;
        }
        for ($i = 0; $i <= $numElements; $i++)
        {
              $data->offsetSet($right, $temp[$right]);
              $right--;
        }
  }